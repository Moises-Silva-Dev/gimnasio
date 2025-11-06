<?php

namespace App\Http\Requests\UserMembership;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserMembershipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    // Verificar si el usuario ya tiene una membresía activa
                    $hasActiveMembership = \App\Models\UserMembership::where('user_id', $value)
                        ->where('status', 'active')
                        ->where('end_date', '>=', now())
                        ->exists();

                    if ($hasActiveMembership) {
                        $fail('Este usuario ya tiene una membresía activa. Debe esperar a que expire o cancelarla primero.');
                    }
                }
            ],
            'membership_id' => ['required', 'exists:memberships,id'],
            'start_date' => ['required', 'date'],
            'payment_amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Debes seleccionar un usuario.',
            'user_id.exists' => 'El usuario seleccionado no es válido.',
            'membership_id.required' => 'Debes seleccionar una membresía.',
            'membership_id.exists' => 'La membresía seleccionada no es válida.',
            'start_date.required' => 'La fecha de inicio es requerida.',
            'start_date.date' => 'La fecha de inicio no es válida.',
            'payment_amount.required' => 'El monto de pago es requerido.',
            'payment_amount.numeric' => 'El monto debe ser un valor numérico.',
            'payment_amount.min' => 'El monto no puede ser negativo.',
        ];
    }
}
