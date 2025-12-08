<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendeurRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'slogan' => 'nullable|string',
            'email' => 'required|email|unique:professionnels,email_pro',
            'telephone' => 'required|digits_between:8,15',
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'autre_contact' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'couverture' => 'nullable|image|max:4096',
            'adresse' => 'required|string|max:255',
            'ville' => 'required|string|max:100',
            'pays' => 'required|string|max:100',
            'num_contribuable' => 'nullable|string|max:100',
        ];
    }
}
