<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_deliveries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shop_id')
                ->constrained('shops')
                ->cascadeOnDelete();

            $table->string('report_type');

            $table->date('period_start');
            $table->date('period_end');

            $table->text('recipient_emails')->nullable();

            $table->string('status')->default('pending');

            $table->timestamp('sent_at')->nullable();

            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->index(
                ['shop_id', 'report_type', 'period_start', 'period_end'],
                'report_delivery_period_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_deliveries');
    }
};
