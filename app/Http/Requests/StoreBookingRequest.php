<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'      => ['required', 'integer', 'exists:users,id'],
            'schedule_id'  => ['required', 'integer'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'status'       => ['nullable', 'in:pending,confirmed,cancelled'],
        ];
    }
}
