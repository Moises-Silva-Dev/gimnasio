<?php

namespace App\Http\Requests;

use DragonCode\Support\Facades\Helpers\Arr;
use Illuminate\Foundation\Http\FormRequest;

class StoreGymRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool { 
        return true;
    }

    public function rules(): array {
        $isCreate = $this->isMethod('post'); // Detectar si es creación

        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:10'],
        ];
    }

    public function messages(): array {
        return [
            'name.required' => 'El nombre de la cuenta es requerido',
            'address.required' => 'El número de empleados de la cuenta es requerido',
            'phone.required' => 'El teléfono es requerido',
        ];
    }
}