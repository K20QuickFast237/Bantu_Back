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
            // Uploads ou contenu texte
            'cv_url' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'motivation_url' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'motivation_text' => 'nullable|string',
            'cv_genere' => 'nullable|boolean',

            // Ce champ ne devrait pas être rempli par le candidat
            'note_ia' => 'prohibited',

            // Optionnel : commentaire de l'employeur, rempli plus tard
            'commentaire_employeur' => 'prohibited',
        ];
    }
}
