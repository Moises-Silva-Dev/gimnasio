<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGymRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        $isUpdate = $this->isMethod('put'); // Detectar si es actualización

        return [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:10',
        ];
    }

    public function messages(): array {
        return [
            'name.required' => 'El nombre del gimnasio es requerido',
            'address.required' => 'La dirección del gimnasio es requerido',
            'phone.required' => 'El teléfono del gimnasio es requerido',
        ];
    }
}
