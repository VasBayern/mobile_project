<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
    protected $stopOnFirstFailure = true;


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'                 => 'required|string|email|exists:users,email',
            'password'              => 'required|string|min:8',
            'password_confirmation' => 'required|string|same:password|min:8',
            'token'                 => 'required|string',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'email'                 => 'Email',
            'password'              => 'Mật khẩu',
            'password_confirmation' => 'Mật khẩu',
            'Token'                 => 'Token'
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
            'min'           => ':attribute phải chứa ít nhất 8 ký tự',
            'same'          => ':attribute không khớp',
            'exists'        => ':attribute không tồn tại',
        ];
    }
}
