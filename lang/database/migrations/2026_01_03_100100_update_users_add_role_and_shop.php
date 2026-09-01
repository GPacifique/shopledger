<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['system_admin', 'shop_admin', 'manager', 'seller', 'accountant', 'user'])->default('user');
            $table->foreignId('shop_id')->nullable()->constrained('shops')->nullOnDelete();
            $table->enum('account_status', ['active', 'suspended'])->default('active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->dropConstrainedForeignId('shop_id');
            $table->dropColumn('account_status');
        });
    }
};
