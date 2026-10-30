<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            // Same rule as registration: 10 digits, unique
            'phone'         => ['nullable', 'digits:10', Rule::unique(User::class)->ignore($this->user()->id)],
            'country'       => ['nullable', 'string', 'max:100'],
            'city'          => ['nullable', 'string', 'max:100'],
            'address'       => ['nullable', 'string', 'max:500'],

            // JPG / PNG only, max 2 MB
            'photo'         => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'remove_photo'  => ['nullable', 'boolean'],
        ];
    }
}