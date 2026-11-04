<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('ordered_by', 120)->nullable()->after('customer_id');
            $table->foreignId('served_by')->nullable()->after('ordered_by')->constrained('users')->nullOnDelete();
            $table->string('table_number', 20)->nullable()->after('served_by');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('served_by');
            $table->dropColumn(['ordered_by', 'table_number']);
        });
    }
};
