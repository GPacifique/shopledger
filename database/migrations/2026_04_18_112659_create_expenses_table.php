<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            // 🏪 Shop relationship
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');

            // 📂 Category relationship
            $table->foreignId('category_id')->nullable()->constrained('expense_categories')->onDelete('set null');

            // 💰 Money spent
            $table->decimal('amount', 12, 2);

            // 📅 IMPORTANT: business date (NOT system date)
            $table->date('expense_date');

            // 🔖 Optional tracking fields
            $table->string('reference')->nullable();
            $table->text('description')->nullable();
//statusactive/inactive

            $table->enum('status', ['paid', 'unpaid'])->default('unpaid');
            // 📎 File attachment (receipt, invoice, etc.)
            $table->string('attachment')->nullable();

            // 👤 Who recorded it
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            // ⏱ System timestamps
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};