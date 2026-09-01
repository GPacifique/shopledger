<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    /**
     * Display a listing of taxes.
     */
    public function index()
    {
        $taxes = Tax::latest()->get();
        return view('taxes.index', compact('taxes'));
    }

    /**
     * Show the form for creating a new tax.
     */
    public function create()
    {
        return view('taxes.create');
    }

    /**
     * Store a newly created tax in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tax_name'   => 'required|string|max:255',
            'tax_rate'   => 'required|numeric|min:0',
            'description'=> 'nullable|string',
            'is_active'  => 'boolean',
        ]);

        Tax::create($validated);

        return redirect()->route('taxes.index')
                         ->with('success', 'Tax created successfully.');
    }

    /**
     * Show the form for editing the specified tax.
     */
    public function edit(Tax $tax)
    {
        return view('taxes.edit', compact('tax'));
    }

    /**
     * Update the specified tax in storage.
     */
    public function update(Request $request, Tax $tax)
    {
        $validated = $request->validate([
            'tax_name'   => 'required|string|max:255',
            'tax_rate'   => 'required|numeric|min:0',
            'description'=> 'nullable|string',
            'is_active'  => 'boolean',
        ]);

        $tax->update($validated);

        return redirect()->route('taxes.index')
                         ->with('success', 'Tax updated successfully.');
    }

    /**
     * Remove the specified tax from storage.
     */
    public function destroy(Tax $tax)
    {
        $tax->delete();

        return redirect()->route('taxes.index')
                         ->with('success', 'Tax deleted successfully.');
    }
}
