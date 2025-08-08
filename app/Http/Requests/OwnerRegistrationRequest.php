<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Otp;

class OwnerRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Anyone can register as an owner
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:owners,phone',
            'district' => 'required|string|max:100',
            'country' => 'nullable|string|max:100',
            'password' => 'required|string|min:6|confirmed',
        ];

        // Check if OTP is required based on settings
        $otpSettings = \App\Models\OtpSetting::getSettings();
        if ($otpSettings->is_enabled && $otpSettings->isOtpRequiredFor('registration')) {
            $rules['otp'] = 'required|string|size:6';
        } else {
            $rules['otp'] = 'nullable|string|size:6';
        }

        return $rules;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check if OTP is required and provided
            $otpSettings = \App\Models\OtpSetting::getSettings();
            if ($otpSettings->is_enabled && $otpSettings->isOtpRequiredFor('registration')) {
                if ($this->has('otp') && !$this->verifyOtp()) {
                    $validator->errors()->add('otp', 'Invalid or expired OTP');
                }
            }
        });
    }

    /**
     * Verify OTP for the phone number
     */
    private function verifyOtp(): bool
    {
        $phone = $this->input('phone');
        $otp = $this->input('otp');

        return Otp::verifyOtp($phone, $otp, 'registration');
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        $messages = [
            'name.required' => 'Name is required.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'phone.required' => 'Phone number is required.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'phone.unique' => 'This phone number is already registered.',
            'district.required' => 'District is required.',
            'district.max' => 'District cannot exceed 100 characters.',
            'country.max' => 'Country name cannot exceed 100 characters.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];

        // Check if OTP is required based on settings
        $otpSettings = \App\Models\OtpSetting::getSettings();
        if ($otpSettings->is_enabled && $otpSettings->isOtpRequiredFor('registration')) {
            $messages['otp.required'] = 'OTP is required.';
            $messages['otp.size'] = 'OTP must be 6 digits.';
        }

        return $messages;
    }
}
