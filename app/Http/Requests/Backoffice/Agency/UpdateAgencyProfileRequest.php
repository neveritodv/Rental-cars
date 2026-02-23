<?php

namespace App\Http\Requests\Backoffice\Agency;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAgencyProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('backoffice')->user()?->hasRole('super-admin', 'backoffice') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nom de l\'agence',
            'email' => 'email',
            'phone' => 'téléphone',
            'address' => 'adresse',
            'city' => 'ville',
            'country' => 'pays',
            'profile_photo' => 'photo de profil',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de l\'agence est obligatoire.',
            'name.max' => 'Le nom de l\'agence ne doit pas dépasser 255 caractères.',
            'email.email' => 'Veuillez fournir une adresse email valide.',
            'phone.max' => 'Le téléphone ne doit pas dépasser 20 caractères.',
            'address.max' => 'L\'adresse ne doit pas dépasser 255 caractères.',
            'profile_photo.image' => 'Le fichier doit être une image.',
            'profile_photo.mimes' => 'L\'image doit être au format: jpeg, png, jpg, gif.',
            'profile_photo.max' => 'L\'image ne doit pas dépasser 2 Mo.',
        ];
    }
}