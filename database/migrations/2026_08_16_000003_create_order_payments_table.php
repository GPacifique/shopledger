<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();

            $table->decimal('amount', 14, 2);
            $table->enum('payment_method', ['cash', 'mobile_money', 'card', 'bank_transfer', 'credit']);
            $table->string('reference')->nullable(); // MoMo code, transaction ref, etc.
            $table->timestamp('paid_at');

            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
