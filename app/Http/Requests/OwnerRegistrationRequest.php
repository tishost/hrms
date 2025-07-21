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
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:owners,phone',
            'address' => 'required|string|max:500',
            'country' => 'required|string|max:100',
            'password' => 'required|string|min:6|confirmed',
            'otp' => 'required|string|size:6',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Verify OTP
            if (!$this->verifyOtp()) {
                $validator->errors()->add('otp', 'Invalid or expired OTP');
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
        return [
            'name.required' => 'Name is required.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'phone.required' => 'Phone number is required.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'phone.unique' => 'This phone number is already registered.',
            'address.required' => 'Address is required.',
            'address.max' => 'Address cannot exceed 500 characters.',
            'country.required' => 'Country is required.',
            'country.max' => 'Country name cannot exceed 100 characters.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'otp.required' => 'OTP is required.',
            'otp.size' => 'OTP must be 6 digits.',
        ];
    }
}
