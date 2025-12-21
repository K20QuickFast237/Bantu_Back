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
            'lieu_travail' => 'required|string|max:255',
            'description_poste' => 'required|string',
            'exigences' => 'required|string',
            'responsabilites' => 'required|string',
            'experience_requise' => 'sometimes|required|string|in:<1an,1-3ans,4-5ans,>5ans',
            'ville' => 'required|string|max:255',
            'pays' => 'required|string|max:255',
            'type_contrat' => 'required|in:CDI,CDD,Interim,Stage,Alternance,Freelance,Autre',
            'remuneration_min' => 'sometimes|numeric|min:0',
            'remuneration_max' => 'sometimes|numeric|gte:remuneration_min',
            'document_annonce' => 'sometimes|file|mimes:pdf,doc,docx|max:2048',

            'date_publication' => 'required|date',
            'date_limite_soumission' => 'required|date|after_or_equal:date_publication',

            // Étape 3 : candidature
            'email_candidature' => 'sometimes|required|email|max:255',
            'url_candidature' => 'sometimes|required|url|max:255',
            'instructions_candidature' => 'sometimes|required|string',
            // 'experience_requise' => 'nullable|required|string',
            'documents_requis' => 'sometimes|array',
            'documents_requis.*' => 'string|max:255',

            // Étape 4 : état de l'offre
            'statut' => 'required|in:active,en_pause,fermee,archivee',
            'nombre_vues' => 'nullable|integer|min:0',

            // Skills (si tu gardes la relation)
            'skills' => 'sometimes|array',
            'skills.*' => 'exists:skills,id',

            'categorie_id' => 'required|exists:offre_categories,id',

        ];
    }
}
