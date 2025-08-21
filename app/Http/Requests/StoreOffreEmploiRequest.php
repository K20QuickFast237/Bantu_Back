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
            'titre_poste' => 'required|string|max:255',
            'description_poste' => 'required|string',
            'exigences' => 'nullable|string',
            'responsabilites' => 'nullable|string',
            'ville' => 'required|string|max:255',
            'pays' => 'required|string|max:255',
            'type_contrat' => 'required|in:cdi,cdd,interim,stage,alternance,freelance,autre',
            'remuneration_min' => 'nullable|numeric|min:0',
            'remuneration_max' => 'nullable|numeric|gte:remuneration_min',
            'date_publication' => 'required|date',
            'date_limite_soumission' => 'required|date|after_or_equal:date_publication',
            'statut' => 'required|in:active,en_pause,fermee,archivee',
            'nombre_vues' => 'nullable|integer|min:0',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id',

        ];
    }
}
