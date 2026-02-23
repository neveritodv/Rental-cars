<?php

namespace App\Http\Requests\Backoffice\Finance\FinancialAccount;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FinancialAccountStoreRequest extends FormRequest
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
                Rule::in(['bank', 'cash', 'other']),
            ],
            'rib' => [
                'nullable',
                'string',
                'max:50',
            ],
            'initial_balance' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999.99',
            ],
            'is_default' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du compte est obligatoire.',
            'name.max' => 'Le nom ne peut pas dépasser 150 caractères.',
            'type.required' => 'Le type de compte est obligatoire.',
            'type.in' => 'Le type sélectionné n\'est pas valide.',
            'rib.max' => 'Le RIB ne peut pas dépasser 50 caractères.',
            'initial_balance.required' => 'Le solde initial est obligatoire.',
            'initial_balance.numeric' => 'Le solde initial doit être un nombre.',
        ];
    }
}