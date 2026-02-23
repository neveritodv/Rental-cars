<?php

namespace App\Http\Requests\Backoffice\VehicleControl;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleControlStoreRequest extends FormRequest
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
            'agency_id' => [
                'sometimes',
                'exists:agencies,id',
            ],
            'rental_contract_id' => [
                'required',
                'exists:rental_contracts,id',
            ],
            'control_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('vehicle_controls', 'control_number'),
            ],
            'vehicle_id' => [
                'required',
                'exists:vehicles,id',
            ],
            'start_mileage' => [
                'required',
                'integer',
                'min:0',
                'max:9999999',
            ],
            'end_mileage' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999999',
                'gte:start_mileage',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'performed_by' => [
                'sometimes',
                'exists:users,id',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'rental_contract_id.required' => 'Le contrat de location est obligatoire.',
            'rental_contract_id.exists' => 'Le contrat de location sélectionné n\'existe pas.',
            
            'control_number.max' => 'Le numéro de contrôle ne peut pas dépasser 50 caractères.',
            'control_number.unique' => 'Ce numéro de contrôle est déjà utilisé.',
            
            'vehicle_id.required' => 'Le véhicule est obligatoire.',
            'vehicle_id.exists' => 'Le véhicule sélectionné n\'existe pas.',
            
            'start_mileage.required' => 'Le kilométrage de départ est obligatoire.',
            'start_mileage.integer' => 'Le kilométrage de départ doit être un nombre entier.',
            'start_mileage.min' => 'Le kilométrage de départ ne peut pas être négatif.',
            
            'end_mileage.integer' => 'Le kilométrage d\'arrivée doit être un nombre entier.',
            'end_mileage.min' => 'Le kilométrage d\'arrivée ne peut pas être négatif.',
            'end_mileage.gte' => 'Le kilométrage d\'arrivée doit être supérieur ou égal au kilométrage de départ.',
            
            'notes.max' => 'Les notes ne peuvent pas dépasser 1000 caractères.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('start_mileage')) {
            $this->merge([
                'start_mileage' => (int) preg_replace('/[^0-9]/', '', $this->start_mileage),
            ]);
        }

        if ($this->has('end_mileage') && !empty($this->end_mileage)) {
            $this->merge([
                'end_mileage' => (int) preg_replace('/[^0-9]/', '', $this->end_mileage),
            ]);
        }
    }
}