<?php

namespace App\Trait;

use Illuminate\Support\Str;

trait setSlugTrait
{
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst($value);
        $this->attributes['slug'] = Str::slug($value);
    }
}
