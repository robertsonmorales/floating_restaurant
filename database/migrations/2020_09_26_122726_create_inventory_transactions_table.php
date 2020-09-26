<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id')->nullable();
            $table->string('product_name')->nullable();
            $table->integer('product_category_id')->nullable();
            $table->string('product_category_name')->nullable();
            $table->string('type')->nullable(); // 1 = deliveries, 2 = damages, 3 = sold out
            $table->integer('qty')->nullable();
            $table->string('unit')->nullable();
            $table->integer('stocks')->default(0)->nullable();
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
        Schema::dropIfExists('inventory_transactions');
    }
}
