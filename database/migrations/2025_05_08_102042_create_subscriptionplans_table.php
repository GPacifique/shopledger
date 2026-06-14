<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

    Schema::create('subscriptionplans', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->decimal('price',10,2);
        $table->enum('billing_cycle',['monthly','yearly']);
        $table->integer('max_users')->default(1);
        $table->integer('max_products')->default(100);
        $table->integer('max_branches')->default(1);
        $table->json('features')->nullable();
        $table->enum('status',['active','inactive'])->default('active');
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptionplans');
    }
};
