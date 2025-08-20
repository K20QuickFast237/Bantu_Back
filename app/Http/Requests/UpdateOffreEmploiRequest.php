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
            'titre_poste' => 'sometimes|string|max:255',
            'description_poste' => 'sometimes|string',
            'exigences' => 'nullable|string',
            'responsabilites' => 'nullable|string',
            'ville' => 'sometimes|string|max:255',
            'pays' => 'sometimes|string|max:255',
            'type_contrat' => 'sometimes|in:cdi,cdd,interim,stage,alternance,freelance,autre',
            'remuneration_min' => 'nullable|numeric|min:0',
            'remuneration_max' => 'nullable|numeric|gte:remuneration_min',
            'date_publication' => 'sometimes|date',
            'date_limite_soumission' => 'sometimes|date|after_or_equal:date_publication',
            'statut' => 'sometimes|in:active,en_pause,fermee,archivee',
            'nombre_vues' => 'nullable|integer|min:0',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id',
        ];
    }
}
