<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare data for validation, accepting either 'otp' or 'otp_code'.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('otp') && !$this->has('otp_code')) {
            $this->merge([
                'otp_code' => $this->input('otp'),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email', 'max:255'],
            'otp_code' => ['required', 'string', 'size:6'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required'    => 'Email address is required.',
            'email.email'       => 'Please provide a valid email address.',
            'otp_code.required' => 'OTP code is required.',
            'otp_code.size'     => 'OTP code must be exactly 6 digits.',
        ];
    }
}
