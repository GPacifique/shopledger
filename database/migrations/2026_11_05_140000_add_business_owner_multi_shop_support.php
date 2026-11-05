<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role')->default('user')->change();
        });

        Schema::create('business_owner_shop', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['business_owner_id', 'shop_id']);
        });

        $owners = DB::table('users')
            ->where('role', 'business_owner')
            ->whereNotNull('shop_id')
            ->get(['id', 'shop_id']);

        foreach ($owners as $owner) {
            DB::table('business_owner_shop')->insertOrIgnore([
                'business_owner_id' => $owner->id,
                'shop_id' => $owner->shop_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $createdShops = DB::table('shops')
            ->join('users', 'users.id', '=', 'shops.created_by')
            ->where('users.role', 'business_owner')
            ->get(['users.id as business_owner_id', 'shops.id as shop_id']);

        foreach ($createdShops as $shop) {
            DB::table('business_owner_shop')->insertOrIgnore([
                'business_owner_id' => $shop->business_owner_id,
                'shop_id' => $shop->shop_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('business_owner_shop');
        DB::table('users')
            ->where('role', 'business_owner')
            ->update(['role' => 'shop_admin']);
    }
};
