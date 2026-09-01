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
        Schema::create('other_incomes', function (Blueprint $table) {
            $table->id();

            // Shop that received the income
            $table->foreignId('shop_id')
                ->constrained('shops')
                ->cascadeOnDelete();

            // Income stream/category belonging to the shop
            $table->foreignId('income_category_id')
                ->nullable()
                ->constrained('income_categories')
                ->nullOnDelete();

            // Amount received
            $table->decimal('amount', 15, 2);

            // Date income was received
            $table->date('income_date');

            // Optional reference number
            $table->string('reference')->nullable();

            // Description/details
            $table->text('description')->nullable();

            // Income status
            $table->enum('status', [
                'received',
                'pending',
                'cancelled',
            ])->default('received');

            // User who recorded the income
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            // Useful indexes
            $table->index(['shop_id', 'income_date']);
            $table->index(['shop_id', 'income_category_id']);
            $table->index(['shop_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_incomes');
    }
};