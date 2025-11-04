<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMembershipRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'gym_id' => ['required', 'integer', 'exists:gyms,id'],
            'name' => ['required', 'string', 'max:255', Rule::unique('memberships')->where('gym_id', $this->gym_id)],
            'description' => ['required', 'string', 'max:255'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:9999'],
            'sessions' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la membresia es requerido',
            'description.required' => 'La descripción de la membresia es requerido',
            'duration_days.required' => 'El numero de duración de la membresia del gimnasio es requerido',
            'sessions.required' => 'El numero de sesiones de la membresia es requerido',
            'price.required' => 'El precio de la membresia del gimnasio es requerido',

            'name.unique' => 'Ya existe una membresía con este nombre en este gimnasio.',
            'price.numeric' => 'El precio debe ser un valor numérico (ej. 199.99).',
            'price.min' => 'El precio no puede ser negativo.',
            'gym_id.required' => 'Debes seleccionar un gimnasio.',
            'gym_id.exists' => 'El gimnasio seleccionado no es válido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'gym_id' => 'gym',
        ];
    }
}
