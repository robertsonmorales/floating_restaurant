<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveredProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivered_products', function (Blueprint $table) {
            $table->id();
            $table->integer('delivery_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('product_name')->nullable();
            $table->integer('qty')->nullable();
            $table->string('unit')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivered_products');
    }
}
