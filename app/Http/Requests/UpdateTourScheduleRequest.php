<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTourScheduleRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tour_id' => 'sometimes|required|exists:tours,tour_id',
            'tour_date' => 'sometimes|required|date',
            'start_time' => 'sometimes|required|date_format:H:i',
            'available_seats' => 'sometimes|required|integer|min:0',
            'price' => 'sometimes|required|numeric|min:0',
        ];
    }
}
