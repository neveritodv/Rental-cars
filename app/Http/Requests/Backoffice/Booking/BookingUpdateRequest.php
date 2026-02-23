<?php

namespace App\Http\Requests\Backoffice\Booking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => [
                'nullable',
                'integer',
                'exists:clients,id',
            ],
            'vehicle_id' => [
                'nullable',
                'integer',
                'exists:vehicles,id',
            ],
            'start_date' => [
                'required',
                'date',
            ],
            'end_date' => [
                'required',
                'date',
                'after:start_date',
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
            'estimated_total' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999.99',
            ],
            'status' => [
                'required',
                Rule::in(['pending', 'confirmed', 'cancelled', 'converted']),
            ],
            'source' => [
                'required',
                Rule::in(['website', 'mobile', 'backoffice', 'other']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.exists' => 'Le client sélectionné n\'existe pas.',
            'vehicle_id.exists' => 'Le véhicule sélectionné n\'existe pas.',
            'end_date.after' => 'La date de fin doit être postérieure à la date de début.',
            'pickup_location.required' => 'Le lieu de prise en charge est obligatoire.',
            'dropoff_location.required' => 'Le lieu de restitution est obligatoire.',
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut sélectionné n\'est pas valide.',
            'source.required' => 'La source est obligatoire.',
            'source.in' => 'La source sélectionnée n\'est pas valide.',
        ];
    }
}