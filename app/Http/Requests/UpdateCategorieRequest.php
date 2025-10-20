<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategorieRequest extends FormRequest
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
        // récupère l'id passé dans l'URL
        $categorieId = $this->route('id');

        return [
            'nom' => 'required|string|max:255|unique:categories,nom,' . $categorieId,
            'description' => 'nullable|string',
        ];
    }
}
