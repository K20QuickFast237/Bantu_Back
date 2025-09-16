<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCandidatureRequest extends FormRequest
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
            'offre_id' => 'required|exists:offre_emplois,id',

            // Mode Upload
            'cv_url'         => 'required_without:cv_genere|file|mimes:pdf,doc,docx|max:2048',
            'motivation_url' => 'nullable|file|mimes:pdf,doc,docx|max:2048',

            // Mode Profil
            // Ici, au lieu de boolean, cv_genere stockera un snapshot JSON → donc string
            'cv_genere'      => 'required_without:cv_url|json',

            // Facultatif : motivation en texte libre
            'motivation_text' => 'nullable|string',

            // Champs réservés au recruteur
            'note_ia'              => 'prohibited',
            'commentaire_employeur'=> 'prohibited',
        ];

    }

    public function messages(): array
    {
        return [
            'cv_url.required_without' => 'Vous devez fournir un CV (upload) ou postuler via votre profil.',
            'cv_genere.required_without' => 'Vous devez fournir un CV (upload) ou postuler via votre profil.',
            'cv_genere.json' => 'Le champ cv_genere doit contenir un JSON valide (snapshot du profil).',
        ];
    }
}
