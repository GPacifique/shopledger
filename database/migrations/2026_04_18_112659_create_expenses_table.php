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
      Schema::create('expenses', function (Blueprint $table) {
    $table->id();

    $table->foreignId('shop_id')->constrained()->cascadeOnDelete(); // 🔥 KEY FIELD

    $table->foreignId('category_id')->constrained('expense_categories')->cascadeOnDelete();

    $table->decimal('amount', 12, 2);
    $table->date('expense_date');

    $table->string('reference')->nullable();
    $table->text('description')->nullable();
    $table->string('attachment')->nullable();

    $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
