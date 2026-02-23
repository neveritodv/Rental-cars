<?php

namespace App\Http\Requests\Backoffice\RentalContract;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RentalContractStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => [
                'required',
                'integer',
                'exists:vehicles,id',
            ],
            'primary_client_id' => [
                'required',
                'integer',
                'exists:clients,id',
            ],
            'secondary_client_id' => [
                'nullable',
                'integer',
                'exists:clients,id',
            ],
            'start_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'start_time' => [
                'required',
                'date_format:H:i',
            ],
            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],
            'end_time' => [
                'nullable',
                'date_format:H:i',
            ],
            'pickup_location' => [
                'required',
                'string',
                'max:255',
            ],
            'dropoff_location' => [
                'required',
                'string',
                'max:255',
            ],
            'daily_rate' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'discount_amount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'deposit_amount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'observations' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'status' => [
                'required',
                Rule::in(['draft', 'pending', 'accepted', 'in_progress', 'completed', 'cancelled']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_id.required' => 'Le véhicule est obligatoire.',
            'vehicle_id.exists' => 'Le véhicule sélectionné n\'existe pas.',
            
            'primary_client_id.required' => 'Le client principal est obligatoire.',
            'primary_client_id.exists' => 'Le client sélectionné n\'existe pas.',
            
            'secondary_client_id.exists' => 'Le client secondaire sélectionné n\'existe pas.',
            
            'start_date.required' => 'La date de début est obligatoire.',
            'start_date.after_or_equal' => 'La date de début doit être aujourd\'hui ou dans le futur.',
            
            'start_time.required' => 'L\'heure de début est obligatoire.',
            
            'end_date.required' => 'La date de fin est obligatoire.',
            'end_date.after' => 'La date de fin doit être postérieure à la date de début.',
            
            'pickup_location.required' => 'Le lieu de prise en charge est obligatoire.',
            'dropoff_location.required' => 'Le lieu de restitution est obligatoire.',
            
            'daily_rate.required' => 'Le tarif journalier est obligatoire.',
            'daily_rate.numeric' => 'Le tarif journalier doit être un nombre.',
            
            'discount_amount.numeric' => 'La remise doit être un nombre.',
            'deposit_amount.numeric' => 'Le dépôt de garantie doit être un nombre.',
        ];
    }
}