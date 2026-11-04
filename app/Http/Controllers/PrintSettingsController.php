<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrintSettingsController extends Controller
{
    public function edit(Request $request): View
    {
        $shop = $request->user()->shop;
        abort_unless($shop, 403, 'No business is associated with your account.');

        return view('shops.print-settings', [
            'shop' => $shop,
            'settings' => $shop->resolvedPrintSettings(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $shop = $request->user()->shop;
        abort_unless($shop, 403, 'No business is associated with your account.');

        $settings = $request->validate([
            'receipt_paper_size' => ['required', 'in:58mm,80mm,custom'],
            'invoice_paper_size' => ['required', 'in:a4,a5,letter,custom'],
            'receipt_custom_width_mm' => ['required', 'numeric', 'min:40', 'max:120'],
            'invoice_custom_width_mm' => ['required', 'numeric', 'min:100', 'max:300'],
            'invoice_custom_height_mm' => ['required', 'numeric', 'min:100', 'max:400'],
            'show_logo' => ['required', 'boolean'],
            'show_customer' => ['required', 'boolean'],
            'show_tax' => ['required', 'boolean'],
            'show_qr' => ['required', 'boolean'],
            'show_cashier' => ['required', 'boolean'],
            'show_contact' => ['required', 'boolean'],
            'receipt_footer' => ['nullable', 'string', 'max:160'],
            'invoice_notes' => ['nullable', 'string', 'max:1000'],
            'invoice_terms' => ['nullable', 'string', 'max:3000'],
            'auto_print' => ['required', 'boolean'],
        ]);

        foreach ([
            'show_logo',
            'show_customer',
            'show_tax',
            'show_qr',
            'show_cashier',
            'show_contact',
            'auto_print',
        ] as $key) {
            $settings[$key] = $request->boolean($key);
        }

        $shop->update(['print_settings' => $settings]);

        return redirect()
            ->route('print-settings.edit')
            ->with('success', 'Printing preferences saved for ' . $shop->business_name . '.');
    }
}
