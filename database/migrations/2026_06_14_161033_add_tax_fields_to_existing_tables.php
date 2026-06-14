<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Products
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('tax_id')
                  ->nullable()
                  ->after('category_id')
                  ->constrained('taxes')
                  ->nullOnDelete();
        });

        // Sales
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('tax_id')
                  ->nullable()
                  ->constrained('taxes')
                  ->nullOnDelete();

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_total', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
        });

        // Purchases
        Schema::table('purchases', function (Blueprint $table) {
            $table->foreignId('tax_id')
                  ->nullable()
                  ->constrained('taxes')
                  ->nullOnDelete();

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_total', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
        });

        // Expenses
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('tax_id')
                  ->nullable()
                  ->constrained('taxes')
                  ->nullOnDelete();

            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
        });

        // Sale Items (if table exists)
        Schema::table('sale_items', function (Blueprint $table) {
            $table->foreignId('tax_id')
                  ->nullable()
                  ->constrained('taxes')
                  ->nullOnDelete();

            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
        });

        // Purchase Items (if table exists)
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->foreignId('tax_id')
                  ->nullable()
                  ->constrained('taxes')
                  ->nullOnDelete();

            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tax_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tax_id');

            $table->dropColumn([
                'subtotal',
                'tax_total',
                'grand_total'
            ]);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tax_id');

            $table->dropColumn([
                'subtotal',
                'tax_total',
                'grand_total'
            ]);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tax_id');

            $table->dropColumn([
                'tax_rate',
                'tax_amount'
            ]);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tax_id');

            $table->dropColumn([
                'tax_rate',
                'tax_amount'
            ]);
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tax_id');

            $table->dropColumn([
                'tax_rate',
                'tax_amount'
            ]);
        });
    }
};