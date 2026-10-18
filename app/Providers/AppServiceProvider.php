<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->hardenApplication();
        $this->defineGates();
        $this->defineRateLimiters();
        $this->shareMaintenanceNotice();
Gate::define('administer-platform', fn (User $user) =>
    strtolower(trim((string) $user->role)) === 'system_admin'
);
        // Fix for MySQL older versions - limit default string length
        Schema::defaultStringLength(191);

        // Bind 'staff' route parameter to User model, limited to the viewer's own shop
        // (system admins can reach any user). Stops opening /staff/{id} of another shop.
        Route::bind('staff', function ($value) {
            $me = auth()->user();

            return User::query()
                ->when(strtolower(trim((string) $me?->role)) !== 'system_admin',
                    fn ($q) => $q->where('shop_id', $me?->shop_id))
                ->findOrFail($value);
        });
    }

    /**
     * Production safety nets.
     */
    private function hardenApplication(): void
    {
        $production = $this->app->isProduction();

        // Generate https:// links and assets in production.
        if ($production) {
            URL::forceHttps();
        }

        // Block destructive Artisan commands (migrate:fresh, db:wipe, ...) on the live database.
        DB::prohibitDestructiveCommands($production);

        // Optional: set STRICT_MODELS=true in your LOCAL .env to make lazy loading,
        // silently discarded attributes and missing attributes throw instead of hiding.
        // Never active in production.
        Model::shouldBeStrict(! $production && (bool) env('STRICT_MODELS', false));

        // Stronger passwords for anything using Password::defaults()
        // (registration, password reset, profile password change).
        Password::defaults(function () use ($production) {
            $rule = Password::min(10)->letters()->mixedCase()->numbers();

            // The compromised-password check calls an external service,
            // so only use it on the live site.
            return $production ? $rule->uncompromised() : $rule;
        });
    }

    /**
     * Authorization gates.
     */
    private function defineGates(): void
    {
        // Only verified system admins may view or change platform settings.
        // The role is normalised so "System_admin" and "system_admin" both pass.
        Gate::define('manage-settings', function (User $user) {
            $isSystemAdmin = strtolower(trim((string) $user->role)) === 'system_admin';

            $verified = ! method_exists($user, 'hasVerifiedEmail') || $user->hasVerifiedEmail();

            return $isSystemAdmin && $verified;
        });
    }

    /**
     * Named rate limiters, referenced as throttle:<name> in routes.
     * Keyed by user ID when logged in, otherwise by IP.
     */
    private function defineRateLimiters(): void
    {
        $key = fn (Request $request) => $request->user()?->id ?: $request->ip();

        // PUT /admin/settings
        RateLimiter::for('settings', fn (Request $request) =>
            Limit::perMinute(10)->by($key($request))
        );

        // Public contact form: stops spam floods (add ->middleware('throttle:contact') to its route).
        RateLimiter::for('contact', fn (Request $request) => [
            Limit::perMinute(3)->by($request->ip()),
            Limit::perDay(20)->by($request->ip()),
        ]);

        // Bulk spreadsheet imports are heavy; keep them from being hammered.
        RateLimiter::for('imports', fn (Request $request) =>
            Limit::perMinute(5)->by($key($request))
        );
    }

    /**
     * Maintenance notice for the main layout (null when off).
     * Wrapped in try/catch so a missing table never breaks every page (e.g. before migrating).
     */
    private function shareMaintenanceNotice(): void
    {
        view()->composer('layouts.app', function ($view) {
            try {
                $notice = Setting::bool('maintenance_mode')
                    ? Setting::get('maintenance_message')
                    : null;
            } catch (\Throwable $e) {
                $notice = null;
            }

            $view->with('maintenanceNotice', $notice);
        });
    }
}