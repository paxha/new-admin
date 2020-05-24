<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Barcode;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Barcode::class, function (Faker $faker) {
    return [
        'barcode' => $faker->md5,
        'price' => $faker->numberBetween(100, 1000),
        'active' => $faker->boolean,
        'created_at' => $faker->dateTime,
        'created_by' => User::all()->random()->id,
        'updated_at' => $faker->dateTime,
        'updated_by' => User::all()->random()->id,
    ];
});
