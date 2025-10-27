<?php

namespace App\Http\Requests;

use DragonCode\Support\Facades\Helpers\Arr;
use Illuminate\Foundation\Http\FormRequest;

class StoreCuentaRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        $isCreate = $this->isMethod('post'); // Detectar si es creación

        return [
            'nombre_cuenta' => ['required', 'string', 'max:255'],
            'numero_empleados' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array {
        return [
            'nombre_cuenta.required' => 'El nombre de la cuenta es requerido',
            'numero_empleados.required' => 'El número de empleados de la cuenta es requerido',
        ];
    }
}