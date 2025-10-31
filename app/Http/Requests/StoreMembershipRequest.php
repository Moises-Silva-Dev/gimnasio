<?php

namespace App\Http\Requests;

use DragonCode\Support\Facades\Helpers\Arr;
use Illuminate\Foundation\Http\FormRequest;

class StoreMembershipRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool { 
        return true;
    }

    public function rules(): array {
        $isCreate = $this->isMethod('post'); // Detectar si es creación

        return [
            'gym_id' => 'required|integer|exists:gyms,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'duration_days' => 'required|string|max:4',
            'sessions' => 'required|string|max:4',
            'price' => 'required|string|max:10',
        ];
    }

    public function messages(): array {
        return [
            'name.required' => 'El nombre del gimnasio es requerido',
            'description.required' => 'La dirección del gimnasio es requerido',
            'duration_days.required' => 'El teléfono del gimnasio es requerido',
            'sessions.required' => 'El teléfono del gimnasio es requerido',
            'price.required' => 'El precio de la membresia del gimnasio es requerido',
        ];
    }

    public function attributes(): array {
        return [
            'gym_id' => 'gym',
        ];
    }
}
