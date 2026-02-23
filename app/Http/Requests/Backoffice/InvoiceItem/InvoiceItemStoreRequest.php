<?php

namespace App\Http\Requests\Backoffice\InvoiceItem;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceItemStoreRequest extends FormRequest
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
            'description.max' => 'La description ne peut pas dépasser 255 caractères.',
            'days_count.integer' => 'Le nombre de jours doit être un nombre entier.',
            'days_count.min' => 'Le nombre de jours doit être supérieur ou égal à 1.',
            'unit_price_ttc.numeric' => 'Le prix unitaire doit être un nombre.',
            'unit_price_ttc.min' => 'Le prix unitaire doit être supérieur ou égal à 0.',
            'quantity.required' => 'La quantité est obligatoire.',
            'quantity.numeric' => 'La quantité doit être un nombre.',
            'quantity.min' => 'La quantité doit être supérieure à 0.',
            'total_ttc.required' => 'Le total TTC est obligatoire.',
            'total_ttc.numeric' => 'Le total TTC doit être un nombre.',
            'total_ht.required' => 'Le total HT est obligatoire.',
            'total_ht.numeric' => 'Le total HT doit être un nombre.',
            'vat_rate.numeric' => 'Le taux de TVA doit être un nombre.',
            'vat_rate.min' => 'Le taux de TVA doit être supérieur ou égal à 0.',
            'vat_rate.max' => 'Le taux de TVA doit être inférieur ou égal à 100.',
        ];
    }
}