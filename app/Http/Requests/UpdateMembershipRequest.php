<?php

namespace App\Http\Requests;

use DragonCode\Support\Facades\Helpers\Arr;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMembershipRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool { 
        return true;
    }

    public function rules(): array {
        $isUpdate = $this->isMethod('put'); // Detectar si es actualización

        return [
            'gym_id' => 'required|integer|exists:gyms,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'duration_days' => 'required|string',
            'sessions' => 'required|string',
            'price' => 'required|string',
        ];
    }

    public function messages(): array {
        return [
            'name.required' => 'El nombre del gimnasio es requerido',
            'description.required' => 'La dirección del gimnasio es requerido',
            'duration_days.required' => 'El numero de duración de la membresia del gimnasio es requerido',
            'sessions.required' => 'El numero de sesiones de la membresia es requerido',
            'price.required' => 'El precio de la membresia del gimnasio es requerido',
        ];
    }

    public function attributes(): array {
        return [
            'gym_id' => 'gym',
        ];
    }
}
