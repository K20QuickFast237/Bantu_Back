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
            // Recruteur : peut modifier statut/note/commentaire via un autre endpoint
            'statut' => 'prohibited',
            'note_ia' => 'prohibited',
            'commentaire_employeur' => 'prohibited',

            // Candidat : peut modifier son mode de candidature
            'cv_url'         => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'motivation_url' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'motivation_text'=> 'nullable|string',

            // Snapshot du profil
            'cv_genere'      => 'nullable|json',
        ];
        
    }

    public function messages(): array
    {
        return [
                'statut.prohibited' => 'Le statut ne peut pas être modifié par un candidat.',
                'note_ia.prohibited' => 'La note IA ne peut pas être modifiée par un candidat.',
                'commentaire_employeur.prohibited' => 'Le commentaire ne peut pas être modifié par un candidat.',
                'cv_genere.json' => 'Le champ cv_genere doit contenir un JSON valide.',
        ];
    }
}
