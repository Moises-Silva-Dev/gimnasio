<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'mother_last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user)
            ],
            'password' => ['nullable', 'string', 'min:8'],
            'roles' => ['required', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ];
        if (in_array('2', $this->roles)) {
            $rules['status'] = ['required', 'boolean'];
            $rules['gym_id'] = ['required', 'exists:gyms,id'];
        } else {
            $rules['status'] = ['nullable'];
            $rules['gym_id'] = ['nullable', 'exists:gyms,id'];
        }
        return $rules;
    }
    public function attributes(): array
    {
        return [
            'name' => 'Nombre',
            'last_name' => 'Apellido Paterno',
            'mother_last_name' => 'Apellido Materno',
            'phone' => 'Número Telefónico',
            'email' => 'Correo Electrónico',
            'roles' => 'Roles',
        ];
    }

}
