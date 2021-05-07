<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

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
            'image'     => 'https://picsum.photos/200',
            'home'      => rand(0, 1),
            'sort_no'   => $number++
        ];
    }
}
