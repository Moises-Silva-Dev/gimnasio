<?php

namespace App\Http\Requests\UserMembership;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserMembershipRequest extends FormRequest
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
}
