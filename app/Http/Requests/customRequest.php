<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class customRequest extends FormRequest
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
            'userEmail'=>'required|email',
            'userFirstName'=>'required',
            'userLastName'=>'required',
            'userContact'=>'required|integer|digits:10|between:1000000000,9999999999',
            'userPassword'=>'required|min:6'
        ];
    }

    // write custom names of :attribute for every mandatory <input name="">
    public function attributes() {
        return [
            'userEmail'=>'User email',
            'userFirstName'=>'User first name',
            'userLastName'=>'User last name',
            'userContact'=>'User contact',
            'userPassword'=>'User password'
        ];
    }

    // override already created error messages inside FormRequest
    public function messages() {
        return [
            'userEmail.required'=>':attribute is required',
            'userEmail.email'=>':attribute should be valid email address',

            'userFirstName.required'=>':attribute is required',
            'userLastName.required'=>':attribute is required',
            
            'userContact.required'=>':attribute is required',
            'userContact.integer'=>':attribute must be in numbers',
            'userContact.digits'=>':attribute must be of 10 digits',
            'userContact.between'=>':attribute number should not start with 0',
            
            'userPassword.required'=>':attribute is required',
            'userPassword.min'=>':attribute must be atleast :min characters',
        ];
    }
}
