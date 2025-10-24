<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOffreEmploiRequest extends FormRequest
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
            // Étape 2 : infos principales
            'titre_poste' => 'sometimes|string|max:255',
            'fonction' => 'sometimes|nullable|string|max:255',
            'lieu_travail' => 'sometimes|nullable|string|max:255',
            'description_poste' => 'sometimes|string',
            'exigences' => 'nullable|string',
            'responsabilites' => 'nullable|string',
            'ville' => 'sometimes|string|max:255',
            'pays' => 'sometimes|string|max:255',
            'type_contrat' => 'sometimes|in:cdi,cdd,interim,stage,alternance,freelance,autre',
            'remuneration_min' => 'nullable|numeric|min:0',
            'remuneration_max' => 'nullable|numeric|gte:remuneration_min',
            'document_annonce' => 'sometimes|file|mimes:pdf,doc,docx|max:2048',

            'date_publication' => 'sometimes|date',
            'date_limite_soumission' => 'sometimes|date|after_or_equal:date_publication',

            // Étape 3 : candidature
            'email_candidature' => 'sometimes|email|max:255',
            'url_candidature' => 'sometimes|nullable|url|max:255',
            'instructions_candidature' => 'sometimes|nullable|string',
            'documents_requis' => 'sometimes|nullable|array',
            'documents_requis.*' => 'string|max:255',

            // Étape 4 : état de l'offre
            'statut' => 'sometimes|in:active,en_pause,fermee,archivee',
            'nombre_vues' => 'nullable|integer|min:0',

            // Skills
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id',

            'categorie_id' => 'sometimes|exists:offre_categories,id',
        ];
    }
}
