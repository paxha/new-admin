<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'attribute_id' => Attribute::all()->random()->id,
        'brand_id' => Brand::all()->random()->id,
        'category_id' => Category::all()->random()->id,
        'unit_id' => Unit::all()->random()->id,
        'name' => $faker->word,
        'handler' => $faker->md5,
        'attribute_value' => $faker->word,
        'description' => $faker->realText(200),
        'available_at' => $faker->randomElement(['online', 'online_plus_offline', 'offline']),
        'type' => $faker->randomElement(['own', 'local', 'international']),
        'continue' => $faker->boolean,
        'active' => $faker->boolean,
        'created_at' => $faker->dateTime,
        'created_by' => User::all()->random()->id,
        'updated_at' => $faker->dateTime,
        'updated_by' => User::all()->random()->id,
    ];
});
