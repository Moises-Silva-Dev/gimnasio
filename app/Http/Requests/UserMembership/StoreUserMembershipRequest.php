<?php

namespace App\Http\Requests\UserMembership;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserMembershipRequest extends FormRequest
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
            'gym_id' => 'required|exists:gyms,id',
            'user_id' => 'required|exists:users,id',
            'membership_id' => 'required|exists:memberships,id',
            'payment_id' => 'nullable|exists:payments,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'amount_paid' => 'required|numeric|min:0',
            'status' => 'required|in:active,expired,pending',
        ];
    }
     public function messages(): array
    {
        return [
           'gym_id.required' => 'Debe seleccionar un gimnasio.',
            'gym_id.exists' => 'El gimnasio seleccionado no existe.',

            'user_id.required' => 'Debe seleccionar el usuario al que pertenece la membresía.',
            'user_id.exists' => 'El usuario seleccionado no es válido.',

            'membership_id.required' => 'Debe seleccionar una membresía válida.',
            'membership_id.exists' => 'La membresía seleccionada no existe.',

            'start_date.required' => 'Debe ingresar la fecha de inicio.',
            'start_date.date' => 'La fecha de inicio no tiene un formato válido.',
            'start_date.before_or_equal' => 'La fecha de inicio no puede ser posterior a la fecha de fin.',

            'end_date.required' => 'Debe ingresar la fecha de fin.',
            'end_date.date' => 'La fecha de fin no tiene un formato válido.',
            'end_date.after_or_equal' => 'La fecha de fin no puede ser anterior a la fecha de inicio.',

            'amount_paid.required' => 'Debe especificar el monto pagado.',
            'amount_paid.numeric' => 'El monto debe ser numérico.',
            'amount_paid.min' => 'El monto no puede ser negativo.',

            'status.required' => 'Debe seleccionar un estado.',
            'status.in' => 'El estado debe ser uno de los siguientes: activo, expirado, cancelado o pendiente.',
        ];
    }
}
