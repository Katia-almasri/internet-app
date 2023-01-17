<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
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
            "email" => 'required|email|max:255|unique:admins,email',
            "password" => "required|min:6|max:51"
        ];
    }

    public function messages()
    {
        return [
           'email.required'=>'email is required',
           'password.required'=>'password news is required'
           
        ];
    }
}
