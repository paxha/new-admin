<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attribute_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();

            $table->string('name');
            $table->string('handler')->unique()->nullable()->comment('Product unique code');
            $table->string('attribute_value')->nullable();
            $table->text('description')->nullable();
            $table->enum('available_at', ['online', 'online_plus_offline', 'offline'])->default('online');
            $table->enum('type', ['own', 'local', 'international'])->default('own');
            $table->boolean('continue')->default(true);
            $table->boolean('active')->default(true);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('no action');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('no action');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('no action');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('no action');

            $table->foreign('created_by')->references('id')->on('users')->onDelete('no action');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('no action');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
