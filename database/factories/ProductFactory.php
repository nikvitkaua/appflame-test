<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'external_id' => null,
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'brand' => $this->faker->randomElement(['Apple', 'Samsung', 'OnePlus', 'Xiaomi']),
            'category' => 'smartphones',
            'sku' => $this->faker->bothify('SKU-####'),
            'price' => $this->faker->randomFloat(2, 100, 2000),
            'discount_percentage' => $this->faker->randomFloat(2, 0, 30),
            'rating' => $this->faker->randomFloat(2, 1, 5),
            'stock' => $this->faker->numberBetween(0, 200),
            'thumbnail' => $this->faker->imageUrl(),
            'images' => [$this->faker->imageUrl(), $this->faker->imageUrl()],
            'tags' => ['smartphones'],
        ];
    }
}
