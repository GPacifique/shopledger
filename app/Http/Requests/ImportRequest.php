<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Secure upload rules for the bulk spreadsheet import.
 * Store the file on a PRIVATE disk under a random name, never under public/:
 *   $path = $request->file('file')->store('imports/' . $request->user()->shop_id, 'local');
 */
class ImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(strtolower(trim((string) $this->user()?->role)), ['shop_admin', 'admin', 'seller'], true);
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:5120',                       // 5 MB
                'mimes:csv,txt,xlsx,xls',         // checks the real content, not just the extension
                'mimetypes:text/csv,text/plain,application/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.max'   => __('The file must be 5 MB or smaller.'),
            'file.mimes' => __('Upload a CSV or Excel file.'),
        ];
    }
}