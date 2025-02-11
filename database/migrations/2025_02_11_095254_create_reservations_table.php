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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('meal_id')->constrained()->onDelete('cascade');
            $table->date('check_in');
            $table->integer('nights');
            $table->integer('adults');
            $table->integer('children');
            $table->boolean('adavance_discount')->default(false);
            $table->boolean('seaplane_discount_type')->default(false);
            $table->boolean('meal_discount_type')->default(false);
            $table->boolean('room_discount_type')->default(false);
            $table->float('seaplane_discount')->default(0);
            $table->float('meal_discount')->default(0);
            $table->float('room_discount')->default(0);
            $table->float('total_without_discount')->default(0);
            $table->float('discounted_amount')->default(0);
            $table->float('total')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
