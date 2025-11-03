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
            'date_naissance' => 'sometimes|required|date',
            'telephone' => 'sometimes|required|digits_between:8,15',
            'adresse' => 'sometimes|required|string|max:255',
            'ville' => 'sometimes|required|string|max:100',
            'pays' => 'sometimes|required|string|max:100',
            'titre_professionnel' => 'sometimes|required|string|max:255',
            'resume_profil' => 'sometimes|required|string',
            'image_profil_file' => 'sometimes|required|image|max:2048',
            'image_profil_link' => 'sometimes|required|exclude_with:image_profil_file|url',
            'cv_file' => 'sometimes|required|file|mimes:pdf,doc,docx|max:2048',
            'cv_link' => 'sometimes|required|exclude_with:cv_file|url',
            'lettre_motivation_file' => 'sometimes|nullable|file|mimes:pdf,doc,docx|max:2048',
            'lettre_motivation_link' => 'sometimes|nullable|exclude_with:lettre_motivation_file|url',
            'is_visible' => 'sometimes|boolean',
            'portfolio' => 'sometimes|required|url',
            'ressources' => 'sometimes|required|json',
        ];
    }
}
