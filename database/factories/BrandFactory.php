<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Brand;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Brand::class, function (Faker $faker) {
    return [
        'name' => $faker->colorName,
        'description' => $faker->realText(200),
        'iso2' => $faker->randomLetter . $faker->randomLetter,
        'active' => $faker->boolean,
        'created_at' => $faker->dateTime,
        'created_by' => User::all()->random()->id,
        'updated_at' => $faker->dateTime,
        'updated_by' => User::all()->random()->id,
    ];
});
