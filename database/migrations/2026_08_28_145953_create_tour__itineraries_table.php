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
        Schema::create('tour_itineraries', function (Blueprint $table) {
            $table->id('itinerary_id');
            $table->foreignId('tour_id')->constrained('tours', 'tour_id')->onDelete('cascade');
            $table->string('day_number');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_itineraries');
    }
};
