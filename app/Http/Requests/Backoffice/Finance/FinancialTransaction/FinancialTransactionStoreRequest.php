<?php

namespace App\Http\Requests\Backoffice\Finance\FinancialTransaction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FinancialTransactionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'financial_account_id' => [
                'required',
                'integer',
                'exists:financial_accounts,id',
            ],
            'transaction_category_id' => [
                'nullable',
                'integer',
                'exists:transaction_categories,id',
            ],
            'date' => [
                'required',
                'date',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999999.99',
            ],
            'type' => [
                'required',
                Rule::in(['income', 'expense']),
            ],
            'description' => [
                'nullable',
                'string',
                'max:255',
            ],
            'reference' => [
                'nullable',
                'string',
                'max:100',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'financial_account_id.required' => 'Le compte est obligatoire.',
            'financial_account_id.exists' => 'Le compte sélectionné n\'existe pas.',
            'transaction_category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'date.required' => 'La date est obligatoire.',
            'amount.required' => 'Le montant est obligatoire.',
            'amount.numeric' => 'Le montant doit être un nombre.',
            'amount.min' => 'Le montant doit être supérieur à 0.',
            'type.required' => 'Le type est obligatoire.',
            'type.in' => 'Le type sélectionné n\'est pas valide.',
            'description.max' => 'La description ne peut pas dépasser 255 caractères.',
            'reference.max' => 'La référence ne peut pas dépasser 100 caractères.',
        ];
    }
}