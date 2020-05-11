<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class User extends FormRequest
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
            'phone' => 'required|number',
            'password' => 'required|min:8|max:16',
        ];
    }

    public function messages()
    {
        return [
            'phone.required' => '手机号必填',
            'phone.number' => '手机号必须为数字',
            'password.required' => '密码必填',
            'password.min' => '密码最低8位',
            'password.max' => '密码最高16位',
        ];
    }
}