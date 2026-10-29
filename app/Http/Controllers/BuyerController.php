<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Marketplace: featured products from approved shops, with the seller
 * (shop + owner) shown on each product card.
 */
class BuyerController extends Controller
{
    private const PER_PAGE  = 24;

    public function dashboard(Request $request)
    {
        $filters = $request->validate([
            'q'         => ['nullable', 'string', 'max:100'],
            'sort'      => ['nullable', Rule::in(['newest', 'price_asc', 'price_desc', 'name'])],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'city'      => ['nullable', 'string', 'max:100'],
            'seller'    => ['nullable', 'integer'],
            'in_stock'  => ['nullable', 'boolean'],
        ]);

        $priceCol    = Schema::hasColumn('products', 'selling_price') ? 'selling_price' : 'price';
        $approvedIds = Shop::select('id')->where('status', 'approved');

        /* ---------------- Product query ---------------- */
        $query = Product::query()
            ->whereIn('shop_id', $approvedIds)
            ->where('is_featured', true)
            ->where('status', 'active');

        if (! empty($filters['seller'])) {
            $query->where('shop_id', (int) $filters['seller']);
        }

        if (! empty($filters['city'])) {
            $query->whereIn('shop_id', Shop::select('id')->where('status', 'approved')->where('city', $filters['city']));
        }

        if ($term = trim($filters['q'] ?? '')) {
            $like    = '%' . addcslashes($term, '%_\\') . '%';
            $hasDesc = Schema::hasColumn('products', 'description');

            $query->where(function ($w) use ($like, $hasDesc) {
                $w->where('name', 'like', $like);

                if ($hasDesc) {
                    $w->orWhere('description', 'like', $like);
                }

                // Buyers can also search by seller name
                $w->orWhereIn('shop_id', Shop::select('id')->where('business_name', 'like', $like));
            });
        }

        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $query->where($priceCol, '>=', $filters['min_price']);
        }
        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $query->where($priceCol, '<=', $filters['max_price']);
        }

        if (! empty($filters['in_stock']) && Schema::hasColumn('products', 'stock')) {
            $query->where(fn ($w) => $w->whereNull('stock')->orWhere('stock', '>', 0));
        }

        match ($filters['sort'] ?? 'newest') {
            'price_asc'  => $query->orderBy($priceCol)->orderBy('id'),
            'price_desc' => $query->orderByDesc($priceCol)->orderBy('id'),
            'name'       => $query->orderBy('name')->orderBy('id'),
            default      => $query->latest('id'),
        };

        $products = $query->paginate(self::PER_PAGE)->withQueryString();

        /* ---------------- Seller details for this page ---------------- */
        $shopIds = $products->pluck('shop_id')->unique()->values();

        $shops   = Shop::whereIn('id', $shopIds)->get()->keyBy('id');
        $counts  = $this->productCounts($shopIds->all());
        $owners  = User::whereIn('id', $shops->pluck('created_by')->filter()->unique())->pluck('name', 'id');

        $cards = $products->getCollection()->map(function (Product $p) use ($shops, $counts, $owners, $priceCol) {
            $shop = $shops->get($p->shop_id);

            return $this->cardPayload($p, $priceCol, $shop ? $this->sellerPayload($shop, $counts[$shop->id] ?? 0, $owners[$shop->created_by] ?? null) : null);
        })->values()->all();

        $heroSlides = Product::query()
            ->whereIn('shop_id', Shop::select('id')->where('status', 'approved'))
            ->where('is_featured', true)
            ->where('status', 'active')
            ->whereNotNull('product_image')
            ->where('product_image', '!=', '')
            ->latest('id')
            ->get(['id', 'name', 'product_image', $priceCol])
            ->map(fn (Product $product) => [
                'name' => $product->name,
                'image' => $product->image_url,
                'price' => (float) $product->getAttribute($priceCol),
            ])
            ->filter(fn (array $slide) => $slide['image'] !== null)
            ->values()
            ->all();

        /* ---------------- Seller banner when browsing one shop ---------------- */
        $activeSeller = null;

        if (! empty($filters['seller'])) {
            $shop = Shop::where('status', 'approved')->find((int) $filters['seller']);

            if ($shop) {
                $activeSeller = $this->sellerPayload(
                    $shop,
                    $this->productCounts([$shop->id])[$shop->id] ?? 0,
                    User::find($shop->created_by)?->name
                );
            }
        }

        /* ---------------- Filter options ---------------- */
        $sellerOptions = Shop::where('status', 'approved')
            ->whereIn('id', Product::select('shop_id')
                ->where('is_featured', true)
                ->where('status', 'active'))
            ->orderBy('business_name')
            ->get(['id', 'business_name', 'city']);

        $cities = Shop::where('status', 'approved')
            ->whereNotNull('city')->where('city', '!=', '')
            ->distinct()->orderBy('city')->pluck('city');

        return view('buyer.buyer-dashboard', [
            'products'      => $products,
            'cards'         => $cards,
            'heroSlides'    => $heroSlides,
            'activeSeller'  => $activeSeller,
            'sellerOptions' => $sellerOptions,
            'cities'        => $cities,
        ]);
    }

    /* ------------------------------------------------------------------
     | Helpers
     ------------------------------------------------------------------ */

    private function productCounts(array $shopIds): array
    {
        if (! $shopIds) {
            return [];
        }

        $query = Product::whereIn('shop_id', $shopIds);
        $query->where('is_featured', true)->where('status', 'active');

        return $query->selectRaw('shop_id, COUNT(*) as c')
            ->groupBy('shop_id')
            ->pluck('c', 'shop_id')
            ->all();
    }

    private function cardPayload(Product $product, string $priceCol, ?array $seller): array
    {
        $a = $product->getAttributes();

        return [
            'id'          => $product->id,
            'name'        => $product->name,
            'price'       => (float) ($a[$priceCol] ?? 0),
            'stock'       => isset($a['stock']) ? (int) $a['stock'] : null,
            'image'       => $product->image_url,
            'description' => isset($a['description']) ? Str::limit(trim(strip_tags((string) $a['description'])), 260) : null,
            'shop'        => $seller,
        ];
    }

    private function sellerPayload(Shop $shop, int $productCount, ?string $owner): array
    {
        $a = $shop->getAttributes();

        return [
            'id'         => $shop->id,
            'name'       => $shop->business_name,
            'type'       => $this->clean($a['business_type'] ?? null),
            'city'       => $this->clean($a['city'] ?? null),
            'country'    => $this->clean($a['country'] ?? null),
            'address'    => $this->clean($a['address'] ?? null),
            'phone'      => $this->clean($a['phone'] ?? null),
            'email'      => $this->clean($a['email'] ?? null),
            'whatsapp'   => $this->whatsappLink($a['phone'] ?? null, $a['country'] ?? null),
            'logo'       => $this->logoUrl($a['logo'] ?? null),
            'registered' => ! empty($a['registration_number']),
            'since'      => $shop->created_at?->format('M Y'),
            'owner'      => $owner,
            'products'   => $productCount,
        ];
    }

    private function clean(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /** wa.me link, only for Rwandan numbers (0788..., 250788..., +250788...). */
    private function whatsappLink(?string $phone, ?string $country): ?string
    {
        if (! $phone || ($country && strtolower(trim($country)) !== 'rwanda')) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);

        if (Str::startsWith($digits, '250')) {
            // already has the country code
        } elseif (Str::startsWith($digits, '0')) {
            $digits = '250' . substr($digits, 1);
        } else {
            return null;
        }

        return strlen($digits) === 12 ? 'https://wa.me/' . $digits : null;
    }

    private function logoUrl(?string $logo): ?string
    {
        if (! $logo) {
            return null;
        }
        if (Str::startsWith($logo, ['http://', 'https://'])) {
            return $logo;
        }

        $logo = ltrim($logo, '/');

        foreach ([$logo, 'storage/' . $logo] as $candidate) {
            if (file_exists(public_path($candidate))) {
                return asset($candidate);
            }
        }

        return null;
    }

}