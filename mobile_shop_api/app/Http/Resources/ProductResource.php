<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'category'      => new CategoryResource($this->category),
            'brand'         => new BrandResource($this->brand),
            'price_core'    => $this->price_core,
            'price'         => $this->price,
            'sort_no'       => $this->sort_no,
            'home'          => $this->home,
            'new'           => $this->new,
            'created_at'    => $this->created_at,
        ];
    }
}
