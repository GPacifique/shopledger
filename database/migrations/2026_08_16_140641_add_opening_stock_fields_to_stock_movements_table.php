<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->string('type')
                ->default('adjustment')
                ->after('product_id');

            $table->decimal('unit_cost', 12, 2)
                ->nullable()
                ->after('quantity_after');

            $table->decimal('total_cost', 14, 2)
                ->nullable()
                ->after('unit_cost');

            $table->timestamp('movement_date')
                ->nullable()
                ->after('total_cost');
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'unit_cost',
                'total_cost',
                'movement_date',
            ]);
        });
    }
};