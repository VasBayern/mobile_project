<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $number = 1;

        return [
            'name'          => $this->faker->text(10),
            'category_id'   => Category::inRandomOrder()->value('id'),
            'brand_id'      => Brand::inRandomOrder()->value('id'),
            'price_core'    => $this->faker->biasedNumberBetween($min = 200000, $max = 250000),
            'price'         => $this->faker->biasedNumberBetween($min = 100000, $max = 200000),
            'sort_no'       => $number++,
            'home'          => rand(0, 1),
            'new'           => rand(0, 1),
            'introduction'  => $this->faker->sentence(50),
            'description'   => $this->faker->sentence(100),
            'specification' => $this->faker->sentence(100),
            'additional_incentives' => $this->faker->sentence(100),
        ];
    }
}
