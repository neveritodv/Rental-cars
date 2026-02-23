<?php

namespace App\Http\Requests\Backoffice\VehicleVignette;

use Illuminate\Foundation\Http\FormRequest;

class VehicleVignetteUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Temporarily set to true for testing
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
            'year' => [
                'required',
                'integer',
                'digits:4',
                'min:2000',
                'max:' . (date('Y') + 1),
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
            
            'year.required' => 'L\'année est obligatoire.',
            'year.integer' => 'L\'année doit être un nombre.',
            'year.digits' => 'L\'année doit comporter 4 chiffres.',
            'year.min' => 'L\'année doit être supérieure ou égale à 2000.',
            'year.max' => 'L\'année ne peut pas dépasser ' . (date('Y') + 1) . '.',
            
            'notes.max' => 'Les notes ne peuvent pas dépasser 1000 caractères.',
        ];
    }
}