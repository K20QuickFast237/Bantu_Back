<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreParticulierRequest extends FormRequest
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
            'date_naissance' => 'required|date',
            'telephone' => 'required|digits_between:8,15',
            'adresse' => 'required|string|max:255',
            'ville' => 'required|string|max:100',
            'pays' => 'required|string|max:100',
            'titre_professionnel' => 'required|string|max:255',
            'resume_profil' => 'required|string',
            'image_profil' => 'nullable|image|max:2048',
            'cv_link' => 'nullable|url',
            'lettre_motivation_link' => 'nullable|url',
            'is_visible' => 'boolean',
        ];
    }
}
