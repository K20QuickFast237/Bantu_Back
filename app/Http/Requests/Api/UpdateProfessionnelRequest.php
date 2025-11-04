<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfessionnelRequest extends FormRequest
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
            'email_pro' => 'sometimes|email|unique:professionnels,email_pro,' . $this->professionnel?->id,
            'telephone_pro' => 'sometimes|digits_between:8,15',
            'nom_entreprise' => 'sometimes|string|max:255',
            'description_entreprise' => 'sometimes|string',
            'site_web' => 'nullable|url',
            'logo' => 'nullable|image|max:2048',
            'photo_couverture' => 'nullable|image|max:4096',
            'adresse' => 'sometimes|string|max:255',
            'ville' => 'sometimes|string|max:100',
            'pays' => 'sometimes|string|max:100',
            'num_contribuable' => 'nullable|string|max:100',
        ];
    }
}
