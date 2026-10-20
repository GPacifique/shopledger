<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    /**
     * Switch the application language.
     */
    public function switch(Request $request, string $locale)
    {
        // Validate the selected language.
        if (!array_key_exists($locale, SetLocale::AVAILABLE_LOCALES)) {
            return back()->with(
                'error',
                __('Invalid language selection.')
            );
        }

        // Save the selected language in the session.
        $request->session()->put('locale', $locale);

        // Apply the language immediately.
        App::setLocale($locale);

        // Save the preference for authenticated users.
        if ($request->user()) {
            $request->user()->update([
                'locale' => $locale,
            ]);
        }

        return back()->with(
            'success',
            __('Language changed successfully.')
        );
    }

    /**
     * Get the available application languages.
     */
    public static function getAvailableLocales(): array
    {
        return SetLocale::AVAILABLE_LOCALES;
    }
}
