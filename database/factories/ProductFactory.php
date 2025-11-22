<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Models\Product>
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words( 2, true ),
            'description' => $this->faker->sentence( 10 ),
            'price' => $this->faker->randomFloat( 2, 5, 100 ),
            'discount' => $this->faker->randomElement( [ 0, 0, 0, $this->faker->numberBetween( 5, 50 ) ] ),
//            'priority' => $this->faker->numberBetween( 1, 10 ),
        ];
    }
}
