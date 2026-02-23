<?php

namespace App\Http\Requests\Backoffice\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_id' => [
                'nullable',
                'integer',
                'exists:invoices,id',
            ],
            'rental_contract_id' => [
                'nullable',
                'integer',
                'exists:rental_contracts,id',
            ],
            'financial_account_id' => [
                'required',
                'integer',
                'exists:financial_accounts,id',
            ],
            'payment_date' => [
                'required',
                'date',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999999.99',
            ],
            'method' => [
                'required',
                Rule::in(['cash', 'card', 'bank_transfer', 'cheque', 'other']),
            ],
            'status' => [
                'required',
                Rule::in(['pending', 'confirmed', 'refunded']),
            ],
            'reference' => [
                'nullable',
                'string',
                'max:100',
            ],
            'currency' => [
                'required',
                'string',
                'max:3',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'financial_account_id.required' => 'Le compte financier est obligatoire.',
            'financial_account_id.exists' => 'Le compte sélectionné n\'existe pas.',
            'payment_date.required' => 'La date de paiement est obligatoire.',
            'amount.required' => 'Le montant est obligatoire.',
            'amount.min' => 'Le montant doit être supérieur à 0.',
            'method.required' => 'Le mode de paiement est obligatoire.',
            'method.in' => 'Le mode de paiement sélectionné n\'est pas valide.',
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut sélectionné n\'est pas valide.',
            'currency.required' => 'La devise est obligatoire.',
        ];
    }
}