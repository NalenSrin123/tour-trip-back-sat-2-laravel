<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('booking_id');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'aba_pay', 'simulation', 'credit_card']);
            $table->decimal('amount', 10, 2);
            $table->string('payment_status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->string('transaction_ref')->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
