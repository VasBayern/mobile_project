<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = false;


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'         => 'required|string|email',
            'password'      => 'required|string|min:8',
            'device_name'   => 'required|string',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // $validator->errors()->add('field', 'Something is wrong with this field!');
        });
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'email'         => 'Email',
            'password'      => 'Mật khẩu',
            'device_name'   => 'Trình duyệt'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required'      => ':attribute không được bỏ trống',
            'string'        => ':attribute phải là dạng chuỗi',
            'email'         => ':attribute không đúng định dạng',
            'password.min'  => ':attribute phải chứa ít nhất 8 ký tự',
        ];
    }
}
