<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Available application languages.
     */
    public const AVAILABLE_LOCALES = [
        'en' => 'English',
        'fr' => 'Français',
        'rw' => 'Kinyarwanda',
        'sw' => 'Kiswahili',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Prefer the authenticated user's saved language.
        if ($request->user() && $request->user()->locale) {
            $locale = $request->user()->locale;
        }
        // 2. Otherwise, use the language selected in the session.
        elseif ($request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
        }
        // 3. Otherwise, detect the browser's preferred language.
        else {
            $locale = $this->parseAcceptLanguage(
                $request->header('Accept-Language', '')
            );
        }

        // Validate the selected language.
        if (!array_key_exists($locale, self::AVAILABLE_LOCALES)) {
            $locale = config('app.locale', 'en');

            if (!array_key_exists($locale, self::AVAILABLE_LOCALES)) {
                $locale = 'en';
            }
        }

        // Apply the language to the current request.
        App::setLocale($locale);

        // Persist the resolved language in the session.
        $request->session()->put('locale', $locale);

        return $next($request);
    }

    /**
     * Detect the best supported browser language.
     */
    protected function parseAcceptLanguage(string $header): string
    {
        if (trim($header) === '') {
            return config('app.locale', 'en');
        }

        $availableLocales = array_keys(self::AVAILABLE_LOCALES);

        // Let Laravel parse language preferences and quality values.
        $preferredLanguages = $this->parseLanguagePreferences($header);

        foreach ($preferredLanguages as $language) {
            $language = strtolower(trim($language));

            // Support language tags such as fr-FR and rw-RW.
            $languageCode = explode('-', $language)[0];

            if (in_array($languageCode, $availableLocales, true)) {
                return $languageCode;
            }
        }

        return config('app.locale', 'en');
    }

    /**
     * Sort browser languages by their quality preference.
     */
    protected function parseLanguagePreferences(string $header): array
    {
        $languages = [];

        foreach (explode(',', $header) as $item) {
            $parts = explode(';', trim($item));
            $language = trim($parts[0]);
            $quality = 1.0;

            foreach (array_slice($parts, 1) as $parameter) {
                if (preg_match('/^\s*q\s*=\s*(0(?:\.\d+)?|1(?:\.0+)?)\s*$/i', $parameter, $matches)) {
                    $quality = (float) $matches[1];
                    break;
                }
            }

            if ($language !== '' && $quality > 0) {
                $languages[] = [
                    'language' => $language,
                    'quality' => $quality,
                ];
            }
        }

        usort(
            $languages,
            fn (array $a, array $b) => $b['quality'] <=> $a['quality']
        );

        return array_column($languages, 'language');
    }
}
