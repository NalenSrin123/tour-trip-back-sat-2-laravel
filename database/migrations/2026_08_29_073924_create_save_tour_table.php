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
        Schema::create('saved_tours', function (Blueprint $table) {
            $table->id('saved_id');
            
            // Foreign key referencing users table
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Foreign key referencing tours table
            $table->foreignId('tour_id')->constrained('tours', 'tour_id')->onDelete('cascade');
            
            $table->timestamp('saved_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_tours');
    }
};