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
    Schema::create('shop_subscriptions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('shop_id')->constrained()->onDelete('cascade');
        $table->string('plan');
        $table->string('status')->default('active');
        $table->date('start_date');
        $table->date('end_date');
        $table->decimal('amount', 10, 2)->default(0);
        $table->timestamps();
    });
}
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_subscriptions');
    }
};
