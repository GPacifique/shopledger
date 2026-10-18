<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key', 64)->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Starting values so the settings page is never empty on first load.
        $now = now();

        DB::table('settings')->insert([
            ['key' => 'app_name',            'value' => 'MahWi', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'default_locale',      'value' => 'en',    'created_at' => $now, 'updated_at' => $now],
            ['key' => 'currency',            'value' => 'RWF',   'created_at' => $now, 'updated_at' => $now],
            ['key' => 'support_email',       'value' => null,    'created_at' => $now, 'updated_at' => $now],
            ['key' => 'support_phone',       'value' => null,    'created_at' => $now, 'updated_at' => $now],
            ['key' => 'monthly_price',       'value' => '0',     'created_at' => $now, 'updated_at' => $now],
            ['key' => 'trial_days',          'value' => '0',     'created_at' => $now, 'updated_at' => $now],
            ['key' => 'registration_open',   'value' => '1',     'created_at' => $now, 'updated_at' => $now],
            ['key' => 'maintenance_mode',    'value' => '0',     'created_at' => $now, 'updated_at' => $now],
            ['key' => 'maintenance_message', 'value' => null,    'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};