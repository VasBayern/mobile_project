<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'name'          => 'required|string|unique:products,name',
            'category_id'   => 'required|integer',
            'brand_id'      => 'required|integer',
            'price_core'    => 'required|numeric|multiple_of:1000',
            'price'         => 'required|numeric|multiple_of:1000',
            'sort_no'       => 'required|numeric',
            'home'          => 'required|integer',    // 0: False, 1: True
            'new'           => 'required|integer',    // 0: False, 1: True
            'introduction'  => 'required|string',
            'additional_incentives' => 'required|string',
            'description'   => 'required|string',
            'specification' => 'required|string',
            // 'images'        => 'required|mimes:jpeg,jpg,png,gif',
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
            'name'          => 'Tên sản phẩm',
            'category_id'   => 'ID danh mục',
            'brand_id'      => 'ID hãng',
            'price_core'    => 'Giá gốc',
            'price'         => 'Giá bán',
            'images'        => 'Ảnh',
            'sort_no'       => 'Thứ tự',
            'home'          => 'Hiển thị',
            'new'           => 'SP mới',
            'introduction'  => 'Giới thiệu',
            'additional_incentives' => 'Ưu đãi',
            'description'   => 'Chi tiết',
            'specification' => 'Thông số kĩ thuật'
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
            'unique'        => ':attribute đã tồn tại',
            'multiple_of'   => ':attribute phải là bội số của 1000',
        ];
    }
}
