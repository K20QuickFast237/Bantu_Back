<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCandidatureRecruteurRequest extends FormRequest
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
            'note_ia' => 'nullable|numeric|min:0|max:100',
            'commentaire_employeur' => 'nullable|string',
            'date_entretien' => 'sometimes|required|date|after:now',
            'mode_entretien' => 'sometimes|required|string',
            'lieu_entretien' => 'sometimes|required|string',

            // Champs interdits pour un recruteur
            'cv_url' => 'prohibited',
            'motivation_url' => 'prohibited',
            'motivation_text' => 'prohibited',
            'cv_genere' => 'prohibited',
        ];
    }
}
