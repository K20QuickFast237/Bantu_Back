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
            'cv_file'         => 'required_without:cv_genere|file|mimes:pdf,doc,docx|max:2048',
            'motivation_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'autres_documents' => 'nullable|array',
            'autres_documents.*' => 'file|mimes:pdf,doc,docx,jpg,png|max:4096',

            // Mode Profil
            // Ici, au lieu de boolean, cv_genere stockera un snapshot JSON → donc string
            'cv_genere'      => 'required_without:cv_file|json',

            // Facultatif : motivation en texte libre
            'motivation_text' => 'nullable|string',

            // Champs réservés au recruteur
            'note_ia'              => 'prohibited',
            'commentaire_employeur'=> 'prohibited',
            
            // Documents de candidature requis
            'doc_titre*' => 'nullable|required|string',
            'doc*' => 'nullable|required|file|mimes:pdf,doc,docx,jpg,png|max:4096',
        ];

    }

    public function messages(): array
    {
        return [
            'cv_file.required_without' => 'Vous devez fournir un CV (upload) ou postuler via votre profil.',
            'cv_genere.required_without' => 'Vous devez fournir un CV (upload) ou postuler via votre profil.',
            'cv_genere.json' => 'Le champ cv_genere doit contenir un JSON valide (snapshot du profil).',
        ];
    }
}
