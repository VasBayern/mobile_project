<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BrandFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Brand::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $number = 1;
        $name = $this->faker->text(10);

        return [
            'name'      => $name,
            'slug'      => Str::slug($name),
            'image'     => '/storage/hinh-anh/hang-san-xuat/' . $number . '/' . Str::slug($name) . '.png',
            'home'      => rand(0, 1),
            'sort_no'   => $number++
        ];
    }
}
