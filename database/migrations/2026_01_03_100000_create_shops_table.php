<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('business_name');
            $table->string('business_type');
            $table->string('registration_number')->unique();
            $table->string('tin_number')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('country');
            $table->string('city');
            $table->text('address');
            $table->string('logo')->nullable();
            $table->foreignId('subscriptionplan_id')->nullable()->constrained('subscriptionplans');
            $table->string('slug')->unique();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
