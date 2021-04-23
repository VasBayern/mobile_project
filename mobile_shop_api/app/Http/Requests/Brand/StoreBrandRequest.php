<?php

namespace App\Http\Requests\Brand;

use Illuminate\Foundation\Http\FormRequest;

class StoreBrandRequest extends FormRequest
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
            'name'      => 'required|string|unique:categories,name',
            'image'     => 'required|mimes:jpeg,jpg,png,gif',
            'sort_no'   => 'required|numeric',
            'home'      => 'required|integer',    // 0: False, 1: True
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
            'name'      => 'Tên danh mục',
            'image'     => 'Ảnh',
            'sort_no'   => 'Thứ tự',
            'home'      => 'Hiển thị'
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
            'integer'       => ':attribute đã chọn không đúng',
            'numeric'       => ':attribute phải là dạng số',
            'mimes'         => ':attribute không đúng định dạng',
            'unique'        => ':attribute đã tồn tại'
        ];
    }
}
