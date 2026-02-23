<?php

namespace App\Http\Requests\Backoffice\Finance\TransactionCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionCategoryUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:150',
            ],
            'type' => [
                'required',
                Rule::in(['income', 'expense', 'both']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de la catégorie est obligatoire.',
            'type.required' => 'Le type de catégorie est obligatoire.',
            'type.in' => 'Le type sélectionné n\'est pas valide.',
        ];
    }
}