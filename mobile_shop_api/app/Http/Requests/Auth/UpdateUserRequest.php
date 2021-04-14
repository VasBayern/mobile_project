<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name'      => 'string',
            'phone'     => 'digits:10',
            'birthday'  => 'date_format:d/m/Y',
            'sex'       => 'integer',    // 0: Male, 1: Femail, 2: Orther
            'address'   => 'string|max:300',
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
            'name'      => 'Tên',
            'phone'     => 'Số điện thoại',
            'birthday'  => 'Ngày sinh',
            'sex'       => 'Giới tính',
            'address'   => 'Địa chỉ'
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
            'string'        => ':attribute phải là dạng chuỗi',
            'date_format'   => ':attribute không đúng định dạng',
            'integer'       => ':attribute đã chọn không đúng',
            'max'           => ':attribute phải chứa tối đa 300 ký tự',
            'digits'        => ':attribute phải có 10 số'
        ];
    }
}
