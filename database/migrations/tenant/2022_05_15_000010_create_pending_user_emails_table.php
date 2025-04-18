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
        Schema::create('pending_user_emails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('user');
            $table->string('email')->index();
            $table->string('token');
            $table->string('type')->default('pending');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pending_user_emails');
    }
};
