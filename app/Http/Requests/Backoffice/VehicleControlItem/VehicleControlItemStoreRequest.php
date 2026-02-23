<?php

namespace App\Http\Requests\Backoffice\VehicleControlItem;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleControlItemStoreRequest extends FormRequest
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
            'vehicle_control_id' => [
                'required',
                'exists:vehicle_controls,id',
            ],
            'item_key' => [
                'required',
                'string',
                'max:100',
            ],
            'label' => [
                'nullable',
                'string',
                'max:150',
            ],
            'status' => [
                'required',
                Rule::in(['yes', 'no', 'na']),
            ],
            'comment' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'vehicle_control_id.required' => 'Le contrôle est obligatoire.',
            'vehicle_control_id.exists' => 'Le contrôle sélectionné n\'existe pas.',
            
            'item_key.required' => 'La clé de l\'élément est obligatoire.',
            'item_key.max' => 'La clé ne peut pas dépasser 100 caractères.',
            
            'label.max' => 'Le libellé ne peut pas dépasser 150 caractères.',
            
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut sélectionné n\'est pas valide.',
            
            'comment.max' => 'Le commentaire ne peut pas dépasser 255 caractères.',
        ];
    }
}