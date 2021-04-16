<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name'                  => 'required|string',
            'email'                 => 'required|string|email|unique:users',
            'password'              => 'required|string|min:8',
            'password_confirmation' => 'required|string|same:password',
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
            'name'                  => 'Tên',
            'email'                 => 'Email',
            'password'              => 'Mật khẩu',
            'password_confirmation' => 'Mật khẩu'
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
            'unique'        => ':attribute đã tồn tại',
            'min'           => ':attribute phải chứa ít nhất 8 ký tự',
            'same'          => ':attribute không khớp',
        ];
    }
}
