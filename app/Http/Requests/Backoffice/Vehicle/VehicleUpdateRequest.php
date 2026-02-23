<?php

namespace App\Http\Requests\Backoffice\Vehicle;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VehicleUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_model_id' => ['required', 'exists:vehicle_models,id'],
            'registration_number' => [
                'required', 
                'string', 
                'max:50',
                // ✅ UNIQUE VALIDATION - IGNORE CURRENT VEHICLE
                Rule::unique('vehicles')->where(function ($query) {
                    return $query->where('agency_id', Auth::guard('backoffice')->user()->agency_id);
                })->ignore($this->route('vehicle')->id),
            ],
            'registration_city' => ['nullable', 'string', 'max:100'],
            'vin' => ['nullable', 'string', 'max:50'],
            'color' => ['required', 'string', 'max:50'],
            'year' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'status' => ['required', 'in:available,unavailable,maintenance,sold'],
            'current_mileage' => ['nullable', 'integer', 'min:0'],
            'daily_rate' => ['required', 'numeric', 'min:0'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'has_gps' => ['boolean'],
            'has_air_conditioning' => ['boolean'],
            'fuel_policy' => ['nullable', 'in:full_to_full,same_to_same,other'],
            'fuel_level_out' => ['nullable', 'numeric', 'between:0,1'],
            'fuel_level_in' => ['nullable', 'numeric', 'between:0,1'],
            'photos.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_model_id.required' => 'Le modèle du véhicule est obligatoire.',
            'vehicle_model_id.exists' => 'Le modèle sélectionné n\'existe pas.',
            'registration_number.required' => 'Le numéro d\'immatriculation est obligatoire.',
            'registration_number.unique' => 'Ce numéro d\'immatriculation existe déjà pour votre agence. Veuillez en choisir un autre.',
            'registration_number.max' => 'Le numéro d\'immatriculation ne peut pas dépasser 50 caractères.',
            'registration_city.max' => 'La ville d\'immatriculation ne peut pas dépasser 100 caractères.',
            'vin.max' => 'Le VIN ne peut pas dépasser 50 caractères.',
            'color.required' => 'La couleur est obligatoire.',
            'color.max' => 'La couleur ne peut pas dépasser 50 caractères.',
            'year.required' => 'L\'année est obligatoire.',
            'year.integer' => 'L\'année doit être un nombre.',
            'year.min' => 'L\'année doit être supérieure ou égale à 1900.',
            'year.max' => 'L\'année ne peut pas dépasser ' . (date('Y') + 1) . '.',
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut sélectionné n\'est pas valide.',
            'current_mileage.integer' => 'Le kilométrage doit être un nombre entier.',
            'current_mileage.min' => 'Le kilométrage doit être supérieur ou égal à 0.',
            'daily_rate.required' => 'Le prix journalier est obligatoire.',
            'daily_rate.numeric' => 'Le prix journalier doit être un nombre.',
            'daily_rate.min' => 'Le prix journalier doit être supérieur ou égal à 0.',
            'deposit_amount.numeric' => 'La caution doit être un nombre.',
            'deposit_amount.min' => 'La caution doit être supérieure ou égale à 0.',
            'fuel_policy.in' => 'La politique carburant sélectionnée n\'est pas valide.',
            'fuel_level_out.between' => 'Le niveau de carburant doit être entre 0 et 1.',
            'fuel_level_in.between' => 'Le niveau de carburant doit être entre 0 et 1.',
            'photos.*.image' => 'Le fichier doit être une image.',
            'photos.*.mimes' => 'L\'image doit être au format: jpeg, png, jpg, gif.',
            'photos.*.max' => 'L\'image ne peut pas dépasser 2 Mo.',
            'documents.*.mimes' => 'Le document doit être au format: pdf, doc, docx.',
            'documents.*.max' => 'Le document ne peut pas dépasser 5 Mo.',
        ];
    }
}