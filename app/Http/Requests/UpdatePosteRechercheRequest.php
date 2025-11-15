<?php

namespace App\Http\Requests;

use App\Http\Enums\NiveauExp;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePosteRechercheRequest extends FormRequest
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
            'skills' => 'sometimes|required|list',
            'skills.*' => 'exists:skills,nom',
            'localisations' => 'sometimes|required|list',
            'localisations.*' => 'string',
            'type_contrats' => 'sometimes|required|list',
            'type_contrats.*' => 'in:CDI,CDD,Interim,Stage,Alternance,Freelance,Autre',
            'niveau_experience' => [Rule::enum(NiveauExp::class)],
        ];
    }
}
