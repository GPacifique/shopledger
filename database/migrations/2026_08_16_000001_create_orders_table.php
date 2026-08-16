<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();

            // the waiter who took the order
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            // the seller/admin who approved or rejected it — null until acted on
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();

            // set once approval creates the Sale record
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();

            $table->string('order_number');

            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])
                ->default('pending');

            // mirrors Sale's fields exactly, so approval can copy them straight across
            $table->string('payment_method')->nullable(); // cash, momo, bank, card
            $table->string('payment_status')->default('Unpaid'); // Paid, Unpaid, Partial

            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);

            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['shop_id', 'order_number']);
            $table->index(['shop_id', 'status']);
            $table->index(['shop_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
