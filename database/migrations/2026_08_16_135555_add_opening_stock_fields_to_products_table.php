<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('opening_quantity', 15, 2)
                ->default(0)
                ->after('stock');

            $table->decimal('opening_unit_cost', 12, 2)
                ->default(0)
                ->after('opening_quantity');

            $table->decimal('opening_stock_value', 15, 2)
                ->default(0)
                ->after('opening_unit_cost');

            $table->date('opening_stock_date')
                ->nullable()
                ->after('opening_stock_value');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'opening_quantity',
                'opening_unit_cost',
                'opening_stock_value',
                'opening_stock_date',
            ]);
        });
    }
};