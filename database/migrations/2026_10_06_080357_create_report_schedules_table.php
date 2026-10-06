<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shop_id')
                ->constrained('shops')
                ->cascadeOnDelete();

            $table->string('report_type'); // daily, weekly, monthly, yearly

            $table->boolean('is_enabled')->default(true);

            // Time of day the report should be dispatched.
            $table->time('send_at')->default('07:00:00');

            // Optional day settings for future flexibility.
            $table->unsignedTinyInteger('day_of_week')->nullable();
            $table->unsignedTinyInteger('day_of_month')->nullable();

            $table->timestamps();

            $table->unique(
                ['shop_id', 'report_type'],
                'report_schedules_shop_type_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_schedules');
    }
};
