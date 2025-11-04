<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOffreEmploiRequest extends FormRequest
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
            'titre_poste' => 'required|string|max:255',
            'lieu_travail' => 'nullable|string|max:255',
            'description_poste' => 'required|string',
            'exigences' => 'nullable|string',
            'responsabilites' => 'nullable|string',
            'experience_requise' => 'nullable|required|string|in:<1an,1-3ans,4-5ans,>5ans',
            'ville' => 'required|string|max:255',
            'pays' => 'required|string|max:255',
            'type_contrat' => 'required|in:cdi,cdd,interim,stage,alternance,freelance,autre',
            'remuneration_min' => 'nullable|numeric|min:0',
            'remuneration_max' => 'nullable|numeric|gte:remuneration_min',
            'document_annonce' => 'nullable|file|mimes:pdf,doc,docx|max:2048',

            'date_publication' => 'required|date',
            'date_limite_soumission' => 'required|date|after_or_equal:date_publication',

            // Étape 3 : candidature
            'email_candidature' => 'required|email|max:255',
            'url_candidature' => 'nullable|url|max:255',
            'instructions_candidature' => 'nullable|string',
            // 'experience_requise' => 'nullable|required|string',
            'documents_requis' => 'nullable|array',
            'documents_requis.*' => 'string|max:255',

            // Étape 4 : état de l'offre
            'statut' => 'required|in:active,en_pause,fermee,archivee',
            'nombre_vues' => 'nullable|integer|min:0',

            // Skills (si tu gardes la relation)
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id',

            'categorie_id' => 'required|exists:offre_categories,id',

        ];
    }
}
