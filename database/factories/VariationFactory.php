<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use App\Models\Variation;
use Faker\Generator as Faker;

$factory->define(Variation::class, function (Faker $faker) {
    return [
        'slug' => $faker->slug(6),
        'sku' => $faker->md5,
        'description' => $faker->realText(200),
        'price' => $faker->numberBetween(100, 1000),
        'continue' => $faker->boolean,
        'active' => $faker->boolean,
        'created_at' => $faker->dateTime,
        'created_by' => User::all()->random()->id,
        'updated_at' => $faker->dateTime,
        'updated_by' => User::all()->random()->id,
    ];
});
