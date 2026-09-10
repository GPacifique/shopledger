<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->string('import_reference')->nullable()->after('created_by');
            $table->unique(['shop_id', 'import_reference'], 'purchases_shop_import_reference_unique');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->string('import_reference')->nullable()->after('created_by');
            $table->unique(['shop_id', 'import_reference'], 'sales_shop_import_reference_unique');
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropUnique('purchases_shop_import_reference_unique');
            $table->dropColumn('import_reference');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropUnique('sales_shop_import_reference_unique');
            $table->dropColumn('import_reference');
        });
    }
};
