<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Available locales for the application.
     */
    public const AVAILABLE_LOCALES = [
        'en' => 'English',
        'fr' => 'Français',
        'rw' => 'Kinyarwanda',
        'sw' => 'Kiswahili',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check session first
        if (Session::has('locale')) {
            $locale = Session::get('locale');
        }
        // Then check user preference if authenticated
        elseif (auth()->check() && auth()->user()->locale) {
            $locale = auth()->user()->locale;
        }
        // Then check browser preference
        elseif ($request->hasHeader('Accept-Language')) {
            $locale = $this->parseAcceptLanguage($request->header('Accept-Language'));
        }
        // Default to config locale
        else {
            $locale = config('app.locale', 'en');
        }

        // Validate locale
        if (!array_key_exists($locale, self::AVAILABLE_LOCALES)) {
            $locale = config('app.locale', 'en');
        }

        App::setLocale($locale);
        Session::put('locale', $locale);

        return $next($request);
    }

    /**
     * Parse the Accept-Language header and return the best matching locale.
     */
    protected function parseAcceptLanguage(string $header): string
    {
        $availableLocales = array_keys(self::AVAILABLE_LOCALES);
        $languages = explode(',', $header);

        foreach ($languages as $language) {
            $parts = explode(';', $language);
            $lang = strtolower(trim($parts[0]));
            $lang = substr($lang, 0, 2); // Get just the language code

            if (in_array($lang, $availableLocales)) {
                return $lang;
            }
        }

        return config('app.locale', 'en');
    }
}
