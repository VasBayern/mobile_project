<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'category_id'   => $this->category_id,
            'brand_id'      => $this->brand_id,
            'price_core'    => $this->price_core,
            'price'         => $this->price,
            'sort_no'       => $this->sort_no,
            'home'          => $this->home,
            'new'           => $this->new,
            'introduction'  => $this->introduction,
            'additional_incentives' => $this->additional_incentives,
            'description'   => $this->description,
            'specification' => $this->specification,
        ];
    }
}
