<?php

namespace App\Http\Requests\Backoffice\ContractClient;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContractClientStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rental_contract_id' => [
                'required',
                'integer',
                'exists:rental_contracts,id',
            ],
            'client_id' => [
                'required',
                'integer',
                'exists:clients,id',
            ],
            'role' => [
                'required',
                Rule::in(['primary', 'secondary', 'other']),
            ],
            'order' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'rental_contract_id.required' => 'Le contrat est obligatoire.',
            'rental_contract_id.exists' => 'Le contrat sélectionné n\'existe pas.',
            'client_id.required' => 'Le client est obligatoire.',
            'client_id.exists' => 'Le client sélectionné n\'existe pas.',
            'role.required' => 'Le rôle est obligatoire.',
            'role.in' => 'Le rôle sélectionné n\'est pas valide.',
            'order.integer' => 'L\'ordre doit être un nombre entier.',
            'order.min' => 'L\'ordre doit être supérieur ou égal à 1.',
        ];
    }
}