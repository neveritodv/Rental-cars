<?php

namespace App\Http\Requests\Backoffice\VehicleInsurance;

use Illuminate\Foundation\Http\FormRequest;

class VehicleInsuranceUpdateRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'vehicle_id' => [
                'required',
                'integer',
                'exists:vehicles,id',
            ],
            'company_name' => [
                'nullable',
                'string',
                'max:150',
            ],
            'policy_number' => [
                'nullable',
                'string',
                'max:100',
            ],
            'date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999.99',
            ],
            'next_insurance_date' => [
                'required',
                'date',
                'after:date',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'vehicle_id.required' => 'Le véhicule est obligatoire.',
            'vehicle_id.exists' => 'Le véhicule sélectionné n\'existe pas.',
            
            'company_name.max' => 'Le nom de la compagnie ne peut pas dépasser 150 caractères.',
            
            'policy_number.max' => 'Le numéro de police ne peut pas dépasser 100 caractères.',
            
            'date.required' => 'La date est obligatoire.',
            'date.date' => 'La date doit être une date valide.',
            'date.before_or_equal' => 'La date ne peut pas être dans le futur.',
            
            'amount.required' => 'Le montant est obligatoire.',
            'amount.numeric' => 'Le montant doit être un nombre.',
            'amount.min' => 'Le montant doit être supérieur ou égal à 0.',
            'amount.max' => 'Le montant ne peut pas dépasser 9 999 999.99 DH.',
            
            'next_insurance_date.required' => 'La prochaine date d\'assurance est obligatoire.',
            'next_insurance_date.date' => 'La prochaine date doit être une date valide.',
            'next_insurance_date.after' => 'La prochaine date doit être postérieure à la date de début.',
            
            'notes.max' => 'Les notes ne peuvent pas dépasser 1000 caractères.',
        ];
    }
}