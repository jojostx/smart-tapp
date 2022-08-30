<?php

use App\Enums\Models\ReparkRequestStatus;
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
        Schema::create('repark_requests', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('status')->default(ReparkRequestStatus::UNRESOLVED->value);
            $table->foreignId('blocker_access_id')->nullable()->constrained('accesses')->nullOnDelete();
            $table->foreignId('blocker_driver_id')->nullable()->constrained('drivers')->cascadeOnDelete();
            $table->foreignId('blocker_vehicle_id')->nullable()->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('blockee_access_id')->nullable()->constrained('accesses')->nullOnDelete();
            $table->foreignId('blockee_driver_id')->nullable()->constrained('drivers')->cascadeOnDelete();
            $table->foreignId('blockee_vehicle_id')->nullable()->constrained('vehicles')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['blockee_access_id', 'blocker_access_id', 'blockee_driver_id', 'blocker_driver_id', 'blockee_vehicle_id', 'blocker_vehicle_id'], 'repark_a1a2d1d2v1v2_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('repark_requests');
    }
};
