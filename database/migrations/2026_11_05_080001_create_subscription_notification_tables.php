<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->morphs('notifiable');
            $table->text('type');
            $table->longText('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('subscription_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->integer('days_before');
            $table->string('channel');
            $table->date('notification_date');
            $table->timestamp('sent_at');
            $table->timestamps();
            $table->unique(
                ['subscription_id', 'type', 'days_before', 'channel', 'notification_date'],
                'subscription_notification_once_per_day'
            );
        });

        Schema::create('subscription_digest_runs', function (Blueprint $table) {
            $table->id();
            $table->date('digest_date')->unique();
            $table->timestamp('sent_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_digest_runs');
        Schema::dropIfExists('subscription_notifications');
        Schema::dropIfExists('user_notifications');
    }
};
