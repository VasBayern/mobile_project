<?php

namespace App\Http\Requests\Color;

use Illuminate\Foundation\Http\FormRequest;

class StoreColorRequest extends FormRequest
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
            'name'  => 'required|string|unique:colors,name',
            'code'  => 'required|string|max:7|unique:colors,code'
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
            'name'  => 'Màu',
            'code'  => 'Mã màu',
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
            'required'  => ':attribute không được bỏ trống',
            'string'    => ':attribute phải là dạng chuỗi',
            'unique'    => ':attribute đã tồn tại',
            'max'       => ':attribute có tối đa 7 kí tự',
        ];
    }
}
