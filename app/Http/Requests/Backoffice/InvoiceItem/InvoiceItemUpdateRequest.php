<?php

namespace App\Http\Requests\Backoffice\InvoiceItem;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceItemUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_id' => [
                'required',
                'integer',
                'exists:invoices,id',
            ],
            'description' => [
                'required',
                'string',
                'max:255',
            ],
            'days_count' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'unit_price_ttc' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999.99',
            ],
            'quantity' => [
                'required',
                'numeric',
                'min:0.01',
                'max:9999999.99',
            ],
            'total_ttc' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999.99',
            ],
            'total_ht' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999.99',
            ],
            'vat_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'invoice_id.required' => 'La facture est obligatoire.',
            'invoice_id.exists' => 'La facture sélectionnée n\'existe pas.',
            'description.required' => 'La description est obligatoire.',
            'quantity.required' => 'La quantité est obligatoire.',
            'quantity.numeric' => 'La quantité doit être un nombre.',
            'quantity.min' => 'La quantité doit être supérieure à 0.',
            'total_ttc.required' => 'Le total TTC est obligatoire.',
            'total_ht.required' => 'Le total HT est obligatoire.',
        ];
    }
}