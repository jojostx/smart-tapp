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
        Schema::create('credit_cards', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('tenant_id');
            $table->string('first_6');
            $table->string('last_4');
            $table->string('issuer');
            $table->string('country');
            $table->string('type');
            $table->string('token')->unique();
            $table->string('expiry');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credit_cards');
    }
};
