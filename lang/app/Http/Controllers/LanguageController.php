<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch the application language.
     */
    public function switch(Request $request, string $locale)
    {
        // Validate locale
        if (!array_key_exists($locale, SetLocale::AVAILABLE_LOCALES)) {
            return back()->with('error', __('Invalid language selection.'));
        }

        // Set locale in session
        Session::put('locale', $locale);
        App::setLocale($locale);

        // Update user preference if authenticated
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }

        return back()->with('success', __('Language changed successfully.'));
    }

    /**
     * Get available locales.
     */
    public static function getAvailableLocales(): array
    {
        return SetLocale::AVAILABLE_LOCALES;
    }
}
