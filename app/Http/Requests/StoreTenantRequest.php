<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('owner');


    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'first_name'         => 'required|string|max:50',
            'last_name'          => 'required|string|max:50',
            'gender'             => 'required|in:Male,Female,Other',
            'mobile'             => 'required|string|max:20',
            'alt_mobile'         => 'nullable|string|max:20',
            'email'              => 'nullable|email|max:100',
            'nid_number'         => 'required|string|max:30',
            'address'            => 'nullable|string',
            'country'            => 'nullable|string|max:50',
            'occupation'         => 'required|string|max:30',
            'company_name'       => 'nullable|required_if:occupation,Service|string|max:100',
            'total_family_member'=> 'required|integer|min:1',
            'is_driver'          => 'required|boolean',
            'driver_name'        => 'nullable|required_if:is_driver,1|string|max:100',
        ];
    }
}
