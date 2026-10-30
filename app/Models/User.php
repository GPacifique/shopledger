<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'shop_id',
        'account_status',
        'locale',
        'phone',
        'country',
        'city',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Public URL of the profile photo saved as public/images/users/{id}.jpg|jpeg|png,
     * or null when the user has no photo. No database column needed.
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        foreach (['jpg', 'jpeg', 'png'] as $ext) {
            $relative = "images/users/{$this->id}.{$ext}";
            $full = public_path($relative);

            if (is_file($full)) {
                return asset($relative) . '?v=' . filemtime($full);   // ?v= refreshes the browser cache after a change
            }
        }

        return null;
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'created_by');
    }

    public function isSystemAdmin(): bool
    {
        return $this->role === 'system_admin';
    }

    public function isShopAdmin(): bool
    {
        return $this->role === 'shop_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['system_admin', 'shop_admin'], true);
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isSeller(): bool
    {
        return $this->role === 'seller';
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }
}