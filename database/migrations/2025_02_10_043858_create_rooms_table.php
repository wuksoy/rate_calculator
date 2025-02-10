<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->required();
            $table->string('code')->nullable();
            $table->integer('unit')->required();
            $table->string('bedding')->nullable();
            $table->integer('extra_bed')->nullable();
            $table->integer('max_total_occupancy')->nullable();
            $table->integer('max_adult_occupancy')->nullable();
            $table->integer('max_child_occupancy')->nullable();
            $table->float('base_rate_occupancy')->nullable();
            $table->float('size')->nullable();
            $table->float('rate_high_season')->nullable();
            $table->float('rate_low_season')->nullable();
            $table->float('rate_peak_season')->nullable();
            $table->float('rate_shoulder_season')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
