<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'country_code' => 'required',
            'phone_number' => 'required|regex:^\+?[1-9]\d{1,14}$|unique:customers|min:11|max:14',
            'gender' => 'required',
            'birthdate' => 'required|date_format:YYYY-MM-DD|before:today',
            'avatar' => 'required|mimes:jpg,jpeg,png',
            'email' => 'email|unique:customers',
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'blank',
            'last_name.required' => 'blank',

            'country_code.required' => 'blank',
            'country_code.regex' => 'inclusion',
            
            'phone_number.required' => 'blank',
            'phone_number.regex' => 'not_a_number',
            'phone_number.unique' => 'taken',
            'phone_number.min' => 'too_short',
            'phone_number.max' => 'too_long',
            
            'gender.required' => 'inclusion',

            'birthdate.required' => 'blank',
            'birthdate.before' => 'in_the_future',

            'avatar.required' => 'blank',
            'avatar.mimes' => 'invalid_content_type',

            'email.unique' => 'taken',
            'email.email' => 'invalid',

        ];
    }

}
