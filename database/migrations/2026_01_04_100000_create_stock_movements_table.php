<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->enum('type', ['purchase', 'sale', 'adjustment', 'return', 'damage', 'transfer_in', 'transfer_out']);
            $table->integer('quantity'); // positive for in, negative for out
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->string('reference_type')->nullable(); // 'App\Models\Sale', 'App\Models\Purchase', etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['shop_id', 'product_id', 'created_at']);
            $table->index(['type', 'created_at']);
        });

        // Add low stock threshold to products
        Schema::table('products', function (Blueprint $table) {
            $table->integer('low_stock_threshold')->default(10)->after('stock');
            $table->boolean('track_stock')->default(true)->after('low_stock_threshold');
        });

        // Create stock alerts table
        Schema::create('stock_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->enum('alert_type', ['low_stock', 'out_of_stock', 'overstock']);
            $table->integer('current_stock');
            $table->integer('threshold');
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['shop_id', 'is_resolved', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_alerts');
        Schema::dropIfExists('stock_movements');
        
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['low_stock_threshold', 'track_stock']);
        });
    }
};
