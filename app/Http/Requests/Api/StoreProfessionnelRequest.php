<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfessionnelRequest extends FormRequest
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
            'titre_professionnel' => 'nullable|string|max:255',
            'email_pro' => 'required|email|unique:professionnels,email_pro',
            'telephone_pro' => 'required|digits_between:8,15',
            'nom_entreprise' => 'required|string|max:255',
            'description_entreprise' => 'required|string',
            'site_web' => 'nullable|url',
            'logo' => 'nullable|image|max:2048',
            'adresse' => 'required|string|max:255',
            'ville' => 'required|string|max:100',
            'pays' => 'required|string|max:100',
            'num_contribuable' => 'nullable|string|max:100',
        ];
    }
}
