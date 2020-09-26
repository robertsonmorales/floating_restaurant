<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderedMenusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordered_menuses', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->nullable();
            $table->integer('menu_id')->nullable();
            $table->string('menu_name')->nullable();
            $table->integer('unit_price')->nullable();
            $table->integer('qty')->default(1)->nullable();
            $table->integer('total_price')->nullable();
            $table->integer('order_status')->nullable(); // order menu status
            $table->integer('processed_by')->nullable(); // cook
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
        Schema::dropIfExists('ordered_menuses');
    }
}
