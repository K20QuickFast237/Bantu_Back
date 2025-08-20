<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCandidatureRequest extends FormRequest
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
            'statut' => 'sometimes|in:en_revision,preselectionne,invitation_entretien,rejete,embauche',

            // Recruteur : peut mettre note IA et commentaire
            'note_ia' => 'nullable|numeric|min:0|max:100',
            'commentaire_employeur' => 'nullable|string',

            // Candidat : peut mettre à jour ses fichiers ou textes
            'cv_url' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'motivation_url' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'motivation_text' => 'nullable|string',
            'cv_genere' => 'nullable|boolean',
        ];
    }
}
