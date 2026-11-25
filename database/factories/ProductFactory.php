<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
            'sku' => strtoupper(Str::random(8)),
            'name' => ['en' => ucfirst($name), 'ja' => '商品 '.$name, 'vi' => 'Sản phẩm '.$name],
            'description' => ['en' => $this->faker->sentence()],
            'price' => $this->faker->numberBetween(500, 9000),
            'stock' => 50,
            'image_url' => null,
            'is_active' => true,
        ];
    }
}
