<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateParticulierRequest extends FormRequest
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
            'date_naissance' => 'sometimes|date',
            'telephone' => 'sometimes|digits_between:8,15',
            'adresse' => 'sometimes|string|max:255',
            'ville' => 'sometimes|string|max:100',
            'pays' => 'sometimes|string|max:100',
            'titre_professionnel' => 'sometimes|string|max:255',
            'resume_profil' => 'sometimes|string',
            'image_profil' => 'sometimes|nullable|image|max:2048',
            'cv_link' => 'sometimes|nullable|url',
            'lettre_motivation_link' => 'sometimes|nullable|url',
            'portfolio_link' => 'sometimes|nullable|url|max:255',
            'linkedin_link' => 'sometimes|nullable|url|max:255',
            'behance_link' => 'sometimes|nullable|url|max:255',
            'github_link' => 'sometimes|nullable|url|max:255',
            'is_visible' => 'sometimes|boolean',
        ];
    }
}
