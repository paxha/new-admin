<?php

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Attribute::class, 10)->create()->each(function ($attribute) {
            $attribute->categories()->attach(Category::root()->get()->random(rand(1, 4))->pluck('id')->toArray());
            $attribute->units()->attach(Unit::all()->random(rand(1, 4))->pluck('id')->toArray());
        });
    }
}
