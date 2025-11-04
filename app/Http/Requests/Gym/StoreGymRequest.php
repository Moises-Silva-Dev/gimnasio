<?php

namespace App\Http\Requests\Gym;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreGymRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', Rule::unique('gyms', 'name')],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'numeric', 'digits:10', Rule::unique('gyms', 'phone')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del gimnasio es requerido',
            'name.unique' => 'El nombre del gimnasio ya existe',
            'address.required' => 'La dirección del gimnasio es requerido',
            'phone.required' => 'El teléfono del gimnasio es requerido',
            'phone.numeric' => 'El teléfono del gimnasio debe ser un número',
            'phone.unique' => 'El teléfono del gimnasio ya existe',
        ];
    }
}
