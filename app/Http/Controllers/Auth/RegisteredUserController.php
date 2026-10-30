<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    private const LOGO_DIR = 'images/logos';

    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            // Account (user)
            'name'                => ['required', 'string', 'max:255'],
            'email'               => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone'               => ['required', 'digits:10', 'unique:users,phone'],
            'password'            => ['required', 'confirmed', Password::min(8)],
            'terms'               => ['accepted'],

            // Business (shop)
            'business_name'       => ['required', 'string', 'max:255'],
            'business_type'       => ['required', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:100', 'unique:shops,registration_number'],
            'tin_number'          => ['nullable', 'string', 'max:100'],
            'country'             => ['required', 'string', 'max:100'],
            'city'                => ['required', 'string', 'max:100'],
            'address'             => ['required', 'string', 'max:500'],

            // JPG / PNG only, max 2 MB
            'logo'                => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $logoPath = $this->saveLogo($request);   // e.g. images/logos/k3jfx92aqw7bnc1d.png, or null

        try {
            $user = DB::transaction(function () use ($data, $logoPath) {
                // forceFill so this works whatever is (or isn't) in $fillable
                $user = new User();
                $user->forceFill([
                    'name'     => $data['name'],
                    'email'    => $data['email'],
                    'phone'    => $data['phone'],
                    'country'  => $data['country'],
                    'city'     => $data['city'],
                    'address'  => $data['address'],
                    'password' => Hash::make($data['password']),
                    'role'     => 'user', // use the exact role string your RoleMiddleware expects
                ])->save();

                $shop = new Shop();
                $shop->forceFill([
                    'business_name'       => $data['business_name'],
                    'business_type'       => $data['business_type'],
                    // generated here so it never depends on the model having a creating hook
                    'registration_number' => $data['registration_number'] ?: $this->generateRegistrationNumber(),
                    'tin_number'          => $data['tin_number'] ?? null,
                    'email'               => $data['email'],
                    'phone'               => $data['phone'],
                    'country'             => $data['country'],
                    'city'                => $data['city'],
                    'address'             => $data['address'],
                    'logo'                => $logoPath,   // stored as images/logos/xxxx.png|jpg
                    'status'              => 'pending', // a system admin approves it later
                ]);

                // Link shop and user, whichever way your schema stores it
                if (Schema::hasColumn('shops', 'user_id')) {
                    $shop->user_id = $user->id;
                }
                $shop->save();

                if (Schema::hasColumn('users', 'shop_id')) {
                    $user->forceFill(['shop_id' => $shop->id])->save();
                }

                return $user;
            });
        } catch (\Throwable $e) {
            // Don't leave an orphaned logo file if the database write failed
            if ($logoPath) {
                File::delete(public_path($logoPath));
            }
            throw $e;
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    /**
     * Moves the uploaded logo into public/images/logos and returns its public-relative path.
     */
    private function saveLogo(Request $request): ?string
    {
        if (! $request->hasFile('logo')) {
            return null;
        }

        $file = $request->file('logo');
        $name = strtolower(Str::random(16)) . '.' . $file->extension();   // extension comes from the real file type: jpg or png

        File::ensureDirectoryExists(public_path(self::LOGO_DIR));
        $file->move(public_path(self::LOGO_DIR), $name);

        return self::LOGO_DIR . '/' . $name;
    }

    private function generateRegistrationNumber(): string
    {
        do {
            $number = 'REG-' . now()->format('Y') . '-' . strtoupper(Str::random(6));
        } while (Shop::where('registration_number', $number)->exists());

        return $number;
    }
}