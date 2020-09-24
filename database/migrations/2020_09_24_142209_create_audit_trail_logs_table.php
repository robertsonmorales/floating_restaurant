<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditTrailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_trail_logs', function (Blueprint $table) {
            $table->id();
            $table->text('module')->nullable();
            $table->string('username')->nullable();
            $table->text('action_taken')->nullable();
            $table->text('remarks')->nullable();
            $table->string('ip')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_trail_logs');
    }
}
