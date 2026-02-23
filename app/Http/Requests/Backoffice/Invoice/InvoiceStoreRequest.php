<?php

namespace App\Http\Requests\Backoffice\Invoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_date' => [
                'required',
                'date',
            ],
            'rental_contract_id' => [
                'nullable',
                'integer',
                'exists:rental_contracts,id',
            ],
            'client_id' => [
                'nullable',
                'integer',
                'exists:clients,id',
            ],
            'company_name' => [
                'nullable',
                'string',
                'max:150',
            ],
            'company_address' => [
                'nullable',
                'string',
            ],
            'company_phone' => [
                'nullable',
                'string',
                'max:50',
            ],
            'company_email' => [
                'nullable',
                'email',
                'max:150',
            ],
            'vat_rate' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],
            'total_ht' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999.99',
            ],
            'status' => [
                'required',
                Rule::in(['draft', 'sent', 'paid', 'partially_paid', 'cancelled']),
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
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
            'invoice_date.required' => 'La date de facture est obligatoire.',
            'rental_contract_id.exists' => 'Le contrat sélectionné n\'existe pas.',
            'client_id.exists' => 'Le client sélectionné n\'existe pas.',
            'company_name.max' => 'Le nom de l\'entreprise ne peut pas dépasser 150 caractères.',
            'company_phone.max' => 'Le téléphone ne peut pas dépasser 50 caractères.',
            'company_email.email' => 'L\'email doit être une adresse valide.',
            'company_email.max' => 'L\'email ne peut pas dépasser 150 caractères.',
            'vat_rate.required' => 'Le taux de TVA est obligatoire.',
            'vat_rate.numeric' => 'Le taux de TVA doit être un nombre.',
            'vat_rate.min' => 'Le taux de TVA doit être supérieur ou égal à 0.',
            'vat_rate.max' => 'Le taux de TVA doit être inférieur ou égal à 100.',
            'total_ht.required' => 'Le montant HT est obligatoire.',
            'total_ht.numeric' => 'Le montant HT doit être un nombre.',
            'total_ht.min' => 'Le montant HT doit être supérieur ou égal à 0.',
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut sélectionné n\'est pas valide.',
            'notes.max' => 'Les notes ne peuvent pas dépasser 1000 caractères.',
            'currency.required' => 'La devise est obligatoire.',
        ];
    }
}