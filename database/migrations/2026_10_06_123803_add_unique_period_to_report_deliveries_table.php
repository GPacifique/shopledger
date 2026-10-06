<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_deliveries', function (Blueprint $table) {
            $table->unique(
                [
                    'shop_id',
                    'report_type',
                    'period_start',
                    'period_end',
                ],
                'report_delivery_unique_period'
            );
        });
    }

    public function down(): void
    {
        Schema::table('report_deliveries', function (Blueprint $table) {
            $table->dropUnique('report_delivery_unique_period');
        });
    }
};
