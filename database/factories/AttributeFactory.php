<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Attribute;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Attribute::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'active' => $faker->boolean,
        'created_at' => $faker->dateTime,
        'created_by' => User::all()->random()->id,
        'updated_at' => $faker->dateTime,
        'updated_by' => User::all()->random()->id,
    ];
});
