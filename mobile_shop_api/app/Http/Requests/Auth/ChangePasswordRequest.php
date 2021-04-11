<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'old_password'          => 'required|string|min:8',
            'new_password'          => 'required|string|min:8|different:old_password',
            'password_confirmation' => 'required|string|same:new_password',
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
            'old_password'              => 'Mật khẩu cũ',
            'new_password'              => 'Mật khẩu mới',
            'password_confirmation'     => 'Mật khẩu',
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
            'min'           => ':attribute phải chứa ít nhất 8 ký tự',
            'same'          => ':attribute không khớp',
            'different'     => ':attribute phải khác mật khẩu cũ',
        ];
    }
}
