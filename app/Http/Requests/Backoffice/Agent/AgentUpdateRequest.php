<?php

namespace App\Http\Requests\Backoffice\Agent;

use Illuminate\Foundation\Http\FormRequest;

class AgentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'agency_id' => ['required', 'exists:agencies,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'full_name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150', 'unique:agents,email,' . $this->agent->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'agency_id' => 'agence',
            'user_id' => 'utilisateur',
            'full_name' => 'nom complet',
            'email' => 'email',
            'phone' => 'téléphone',
            'notes' => 'notes',
            'avatar' => 'avatar',
            'remove_avatar' => 'supprimer l\'avatar',
        ];
    }

    public function messages(): array
    {
        return [
            'agency_id.required' => 'L\'agence est requise',
            'agency_id.exists' => 'L\'agence sélectionnée n\'existe pas',
            'user_id.exists' => 'L\'utilisateur sélectionné n\'existe pas',
            'full_name.required' => 'Le nom complet est requis',
            'full_name.max' => 'Le nom complet ne peut pas dépasser 150 caractères',
            'email.email' => 'L\'email doit être valide',
            'email.unique' => 'Cet email est déjà utilisé',
            'avatar.image' => 'Le fichier doit être une image',
            'avatar.mimes' => 'Les formats acceptés sont: jpeg, png, jpg, gif',
            'avatar.max' => 'L\'image ne peut pas dépasser 2 MB',
        ];
    }
}
