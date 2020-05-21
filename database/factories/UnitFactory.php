<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Unit;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Unit::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'created_at' => $faker->dateTime,
        'created_by' => User::all()->random()->id,
        'updated_at' => $faker->dateTime,
        'updated_by' => User::all()->random()->id,
    ];
});
