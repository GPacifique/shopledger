<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Settings extends Model
{
    public const CACHE_KEY = 'app_settings';

    /**
     * The ONLY keys that can ever be stored or read. Anything else is rejected,
     * even if it somehow gets past validation.
     */
    public const ALLOWED = [
        'app_name',
        'default_locale',
        'currency',
        'support_email',
        'support_phone',
        'monthly_price',
        'trial_days',
        'registration_open',
        'maintenance_mode',
        'maintenance_message',
    ];

    /** Values used when a key has no row yet. */
    public const DEFAULTS = [
        'app_name'            => 'MahWi',
        'default_locale'      => 'en',
        'currency'            => 'RWF',
        'support_email'       => null,
        'support_phone'       => null,
        'monthly_price'       => 0,
        'trial_days'          => 0,
        'registration_open'   => 1,
        'maintenance_mode'    => 0,
        'maintenance_message' => null,
    ];

    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    /** All allow-listed settings as key => raw string value, cached. */
    public static function cached(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return static::query()
                ->whereIn('key', self::ALLOWED)
                ->pluck('value', 'key')
                ->all();
        });
    }

    /** Raw value (string|null), falling back to DEFAULTS. */
    public static function get(string $key, $default = null)
    {
        static::assertKey($key);

        $stored = static::cached()[$key] ?? null;

        return $stored ?? $default ?? self::DEFAULTS[$key];
    }

    public static function int(string $key): int
    {
        return (int) static::get($key);
    }

    public static function bool(string $key): bool
    {
        return filter_var(static::get($key), FILTER_VALIDATE_BOOLEAN);
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private static function assertKey(string $key): void
    {
        if (! in_array($key, self::ALLOWED, true)) {
            throw new \InvalidArgumentException("Unknown setting key [{$key}].");
        }
    }
}