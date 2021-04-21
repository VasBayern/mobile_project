<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait SlugByNameTrait
{
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst($value);
        $this->attributes['slug'] = Str::slug($value);
    }
}
