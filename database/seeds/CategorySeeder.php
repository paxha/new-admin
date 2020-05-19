<?php

use App\Models\Category;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Category::class, 4)->create()->each(function ($category) {
            $faker = Faker::create();
            for ($index = 1; $index <= 3; $index++) {
                $c = $category->children()->create([
                    'name' => $faker->name
                ]);
                for ($index2 = 1; $index2 <= 5; $index2++) {
                    $c->children()->create([
                        'name' => $faker->name
                    ]);
                }
            }
        });
    }
}
