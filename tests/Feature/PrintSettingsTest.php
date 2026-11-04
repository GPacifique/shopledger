<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrintSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_admin_can_save_business_specific_print_settings(): void
    {
        [$user, $shop] = $this->createShopAdmin('PRINT-001');
        [, $otherShop] = $this->createShopAdmin('PRINT-002');

        $response = $this->actingAs($user)->put(route('print-settings.update'), [
            'receipt_paper_size' => '58mm',
            'invoice_paper_size' => 'a5',
            'receipt_custom_width_mm' => 72,
            'invoice_custom_width_mm' => 180,
            'invoice_custom_height_mm' => 250,
            'show_logo' => 0,
            'show_customer' => 1,
            'show_tax' => 1,
            'show_qr' => 0,
            'show_cashier' => 1,
            'show_contact' => 1,
            'receipt_footer' => 'Thank you.',
            'invoice_notes' => 'Please pay by bank transfer.',
            'invoice_terms' => 'Goods sold are non-refundable.',
            'auto_print' => 0,
        ]);

        $response->assertRedirect(route('print-settings.edit'));
        $this->assertSame('58mm', $shop->fresh()->resolvedPrintSettings()['receipt_paper_size']);
        $this->assertFalse($shop->fresh()->resolvedPrintSettings()['show_logo']);
        $this->assertSame('Thank you.', $shop->fresh()->resolvedPrintSettings()['receipt_footer']);
        $this->assertSame('80mm', $otherShop->fresh()->resolvedPrintSettings()['receipt_paper_size']);

        $this->actingAs($user)
            ->get(route('print-settings.edit'))
            ->assertOk()
            ->assertSee('58 mm thermal')
            ->assertSee('Thank you.');
    }

    public function test_non_admin_shop_member_cannot_change_print_settings(): void
    {
        [, $shop] = $this->createShopAdmin('PRINT-003');
        $user = User::factory()->create([
            'role' => 'seller',
            'shop_id' => $shop->id,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->put(route('print-settings.update'), [])
            ->assertForbidden();
    }

    public function test_receipt_and_invoice_previews_use_saved_transaction_data_and_paper_sizes(): void
    {
        [$user, $shop] = $this->createShopAdmin('PRINT-004');
        $shop->update([
            'print_settings' => array_replace(Shop::defaultPrintSettings(), [
                'invoice_custom_width_mm' => 175,
                'invoice_custom_height_mm' => 260,
            ]),
        ]);
        $category = Category::create(['shop_id' => $shop->id, 'name' => 'Food']);
        $product = Product::create([
            'shop_id' => $shop->id,
            'sku' => 'BILL-001',
            'name' => 'Freshly baked sourdough loaf',
            'category_id' => $category->id,
            'buying_price' => 700,
            'selling_price' => 1250,
            'stock' => 8,
            'quantity' => 8,
        ]);
        $sale = Sale::create([
            'shop_id' => $shop->id,
            'created_by' => $user->id,
            'sale_date' => today(),
            'total_amount' => 2500,
            'payment_method' => 'cash',
            'payment_status' => 'Paid',
        ]);
        SaleItem::create([
            'shop_id' => $shop->id,
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 1250,
            'cost_price_at_sale' => 700,
            'line_total' => 2500,
        ]);

        $this->actingAs($user)
            ->get(route('sales.print', ['sale' => $sale, 'document' => 'receipt', 'paper' => '58mm']))
            ->assertOk()
            ->assertSee('58mm auto', false)
            ->assertSee('Freshly baked sourdough loaf')
            ->assertSee('1,250')
            ->assertSee('RCP-00000001')
            ->assertSee('<svg', false);

        $this->actingAs($user)
            ->get(route('sales.print', ['sale' => $sale, 'document' => 'invoice', 'paper' => 'custom']))
            ->assertOk()
            ->assertSee('175mm 260mm', false)
            ->assertSee('Freshly baked sourdough loaf')
            ->assertSee('INV-00000001')
            ->assertSee('<svg', false);

        $pdf = $this->actingAs($user)->get(route('sales.export', [
            'sale' => $sale,
            'document' => 'invoice',
            'paper' => 'a5',
        ]));
        $pdf->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $pdf->getContent());

        $receiptPdf = $this->actingAs($user)->get(route('sales.export', [
            'sale' => $sale,
            'document' => 'receipt',
            'paper' => '58mm',
        ]));
        $receiptPdf->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $receiptPdf->getContent());

        $this->actingAs($user)
            ->get(route('pos.sales.print', $sale))
            ->assertOk()
            ->assertSee(route('pos.index'), false)
            ->assertSee(str_replace('&', '&amp;', route('pos.sales.export', [
                'sale' => $sale,
                'document' => 'receipt',
                'paper' => '80mm',
            ])), false);

        [$otherBusinessUser] = $this->createShopAdmin('PRINT-005');
        $this->actingAs($otherBusinessUser)
            ->get(route('sales.print', $sale))
            ->assertForbidden();
    }

    private function createShopAdmin(string $registrationNumber): array
    {
        $user = User::factory()->create([
            'role' => 'shop_admin',
            'email_verified_at' => now(),
        ]);
        $shop = Shop::create([
            'business_name' => 'Print Settings Shop ' . $registrationNumber,
            'business_type' => 'retail',
            'registration_number' => $registrationNumber,
            'email' => strtolower($registrationNumber) . '@example.com',
            'phone' => '250788000001',
            'country' => 'Rwanda',
            'city' => 'Kigali',
            'address' => '1 Main Street',
            'slug' => strtolower($registrationNumber),
            'status' => 'approved',
            'created_by' => $user->id,
        ]);
        $user->update(['shop_id' => $shop->id]);

        return [$user, $shop];
    }
}
