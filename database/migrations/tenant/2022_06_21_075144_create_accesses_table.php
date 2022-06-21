<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accesses', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            
            $table->string('url')->unique();

            $table->integer('status')->default(1);
            $table->integer('validity');
            
            $table->foreign('driver_id')->references('id')->on('drivers')->onUpdate('cascade')->onDelete('SET NULL');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onUpdate('cascade')->onDelete('cascade');
            
            $table->foreign('parking_lot_id')->references('id')->on('parking_lots')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('SET NULL');
            $table->foreign('issued_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('SET NULL');
            $table->timestamp('issued_at')->useCurrent();
            
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
        Schema::dropIfExists('accesses');
    }
};
