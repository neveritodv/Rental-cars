<?php

namespace App\Http\Requests\Backoffice\Client;

use Illuminate\Foundation\Http\FormRequest;

class ClientStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'agency_id' => ['required', 'exists:agencies,id'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150', 'unique:clients,email'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'cin_number' => ['nullable', 'string', 'max:50'],
            'cin_valid_until' => ['nullable', 'date'],
            'passport_number' => ['nullable', 'string', 'max:50'],
            'passport_issue_date' => ['nullable', 'date'],
            'driving_license_number' => ['nullable', 'string', 'max:50'],
            'driving_license_issue_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:active,inactive,blacklisted'],
            'notes' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'agency_id' => 'agence',
            'first_name' => 'prénom',
            'last_name' => 'nom',
            'email' => 'email',
            'phone' => 'téléphone',
            'address' => 'adresse',
            'city' => 'ville',
            'country' => 'pays',
            'nationality' => 'nationalité',
            'birth_date' => 'date de naissance',
            'cin_number' => 'numéro CIN',
            'cin_valid_until' => 'CIN valide jusqu\'au',
            'passport_number' => 'numéro passeport',
            'passport_issue_date' => 'date d\'émission du passeport',
            'driving_license_number' => 'numéro permis de conduire',
            'driving_license_issue_date' => 'date d\'émission du permis',
            'status' => 'statut',
            'notes' => 'notes',
            'avatar' => 'avatar',
        ];
    }

    public function messages(): array
    {
        return [
            'agency_id.required' => 'L\'agence est requise',
            'agency_id.exists' => 'L\'agence sélectionnée n\'existe pas',
            'first_name.required' => 'Le prénom est requis',
            'first_name.max' => 'Le prénom ne peut pas dépasser 100 caractères',
            'last_name.required' => 'Le nom est requis',
            'last_name.max' => 'Le nom ne peut pas dépasser 100 caractères',
            'phone.required' => 'Le téléphone est requis',
            'phone.max' => 'Le téléphone ne peut pas dépasser 50 caractères',
            'email.email' => 'L\'email doit être valide',
            'email.unique' => 'Cet email est déjà utilisé',
            'status.in' => 'Le statut sélectionné est invalide',
            'avatar.image' => 'Le fichier doit être une image',
            'avatar.mimes' => 'Les formats acceptés sont: jpeg, png, jpg, gif',
            'avatar.max' => 'L\'image ne peut pas dépasser 2 MB',
            'birth_date.date' => 'La date de naissance doit être une date valide',
            'cin_valid_until.date' => 'La date doit être valide',
            'passport_issue_date.date' => 'La date doit être valide',
            'driving_license_issue_date.date' => 'La date doit être valide',
        ];
    }
}
