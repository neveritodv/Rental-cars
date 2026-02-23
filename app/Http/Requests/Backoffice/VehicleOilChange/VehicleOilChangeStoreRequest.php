<?php

namespace App\Http\Requests\Backoffice\VehicleOilChange;

use Illuminate\Foundation\Http\FormRequest;

class VehicleOilChangeStoreRequest extends FormRequest
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
            'mileage' => [
                'required',
                'integer',
                'min:0',
                'max:9999999',
            ],
            'next_mileage' => [
                'required',
                'integer',
                'gt:mileage',
                'max:9999999',
            ],
            'mechanic_name' => [
                'nullable',
                'string',
                'max:150',
            ],
            'observations' => [
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
            
            'mileage.required' => 'Le kilométrage est obligatoire.',
            'mileage.integer' => 'Le kilométrage doit être un nombre entier.',
            'mileage.min' => 'Le kilométrage doit être supérieur ou égal à 0.',
            'mileage.max' => 'Le kilométrage ne peut pas dépasser 9 999 999 km.',
            
            'next_mileage.required' => 'Le prochain kilométrage est obligatoire.',
            'next_mileage.integer' => 'Le prochain kilométrage doit être un nombre entier.',
            'next_mileage.gt' => 'Le prochain kilométrage doit être supérieur au kilométrage actuel.',
            'next_mileage.max' => 'Le prochain kilométrage ne peut pas dépasser 9 999 999 km.',
            
            'mechanic_name.max' => 'Le nom du mécanicien ne peut pas dépasser 150 caractères.',
            
            'observations.max' => 'Les observations ne peuvent pas dépasser 1000 caractères.',
        ];
    }
}