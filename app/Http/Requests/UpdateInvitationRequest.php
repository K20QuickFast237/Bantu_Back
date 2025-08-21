<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvitationRequest extends FormRequest
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
            'candidature_id' => 'sometimes|exists:candidatures,id',
            'employeur_id' => 'sometimes|exists:professionnels,id',
            'date_heure_entretien' => 'sometimes|date',
            'type_entretien' => 'sometimes|in:presentiel,telephonique,visio',
            'lieu' => 'nullable|string',
            'lien_visio' => 'nullable|string',
            'instruction_supl' => 'nullable|string',
            'statut' => 'sometimes|in:envoyee,acceptee,refusee,annulee',
            'date_envoi' => 'sometimes|date',
        ];
    }
}
