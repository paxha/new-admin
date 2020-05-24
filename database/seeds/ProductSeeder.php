<?php

use App\Models\Attribute;
use App\Models\Barcode;
use App\Models\Product;
use App\Models\Variation;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        factory(Product::class, 3000)->create()->each(function ($product) use ($faker) {
            $randomAttributes = Attribute::all()->random(rand(0, 3))->pluck('id')->toArray();
            $product->attributes()->attach($randomAttributes);
            $product->variations()->saveMany(factory(Variation::class, 5)->make())->each(function ($variation) use ($faker, $randomAttributes) {
                $variation->attributes()->attach($randomAttributes, ['value' => $faker->word]);
                $variation->barcodes()->saveMany(factory(Barcode::class, 2)->make());
            });
        });
    }
}
