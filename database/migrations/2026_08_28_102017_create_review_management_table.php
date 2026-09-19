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
        Schema::create('review_management', function (Blueprint $table) {
            $table->id('review_mgmt_id');
            $table->enum('review_type', ['tour', 'booking']);
            $table->unsignedBigInteger('review_id');
            $table->enum('status', ['pending', 'approved', 'hidden'])->default('pending');
            $table->foreignId('actioned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('actioned_at')->nullable();
            $table->text('note')->nullable();

            $table->index(['review_type', 'review_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_management');
    }
};
