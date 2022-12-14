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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('tenant_id');
            $table->string('amount');
            $table->string('organization', 255);
            $table->string('name', 255);
            $table->string('email', 255);
            $table->string('tax_number', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('zip_code', 255)->nullable();
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
        Schema::dropIfExists('receipts');
    }
};
