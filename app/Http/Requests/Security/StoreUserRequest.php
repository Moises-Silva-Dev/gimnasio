<?php

namespace App\Http\Requests\Security;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'phone' => ['required', 'integer', 'digits:10'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
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

    public function messages()
    {
        return [
            'name.required' => 'El campo nombre es obligatorio.',
            'last_name.required' => 'El campo apellido paterno es obligatorio.',
            'mother_last_name' => 'El campo apellido materno es obligatorio.',
            'phone.required' => 'El campo número telefónico es obligatorio.',
            'phone.digits' => 'El número de teléfono debe contener exactamente 10 dígitos.',
            'email' => 'El campo Correo Electronico es obligatorio.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password' => 'El campo contraseña  es obligatorio.',
            'password.min' => 'Debe tener al menos 8 caracteres',


            'roles.required' => 'Debes asignar al menos un rol al usuario.',
            'roles.array' => 'El formato de roles no es válido.',
            'roles.*.exists' => 'Alguno de los roles seleccionados no existe en el sistema.',
            'gym_id.required' => 'El campo gym es obligatorio cuando el rol es Miembro.',

        ];
    }
}
