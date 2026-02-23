<?php

namespace App\Http\Requests\Backoffice\VehicleTechnicalCheck;

use Illuminate\Foundation\Http\FormRequest;

class VehicleTechnicalCheckStoreRequest extends FormRequest
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
            'next_check_date' => [
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
            
            'date.required' => 'La date est obligatoire.',
            'date.date' => 'La date doit être une date valide.',
            'date.before_or_equal' => 'La date ne peut pas être dans le futur.',
            
            'amount.required' => 'Le montant est obligatoire.',
            'amount.numeric' => 'Le montant doit être un nombre.',
            'amount.min' => 'Le montant doit être supérieur ou égal à 0.',
            'amount.max' => 'Le montant ne peut pas dépasser 9 999 999.99 DH.',
            
            'next_check_date.required' => 'La prochaine date de contrôle est obligatoire.',
            'next_check_date.date' => 'La prochaine date doit être une date valide.',
            'next_check_date.after' => 'La prochaine date doit être postérieure à la date du contrôle.',
            
            'notes.max' => 'Les notes ne peuvent pas dépasser 1000 caractères.',
        ];
    }
}