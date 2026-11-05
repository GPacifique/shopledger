<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('subscription_plans')) {
            return;
        }

        $addSlug = ! Schema::hasColumn('subscription_plans', 'slug');
        $addBillingPeriod = ! Schema::hasColumn('subscription_plans', 'billing_period');
        $addActive = ! Schema::hasColumn('subscription_plans', 'is_active');

        Schema::table('subscription_plans', function (Blueprint $table) use ($addSlug, $addBillingPeriod, $addActive): void {
            if ($addSlug) {
                $table->string('slug')->nullable();
            }
            if ($addBillingPeriod) {
                $table->string('billing_period')->nullable();
            }
            if ($addActive) {
                $table->boolean('is_active')->nullable();
            }
        });

        foreach (DB::table('subscription_plans')->get() as $plan) {
            $updates = [];

            if ($addSlug || empty($plan->slug)) {
                $updates['slug'] = (Str::slug($plan->name ?? '') ?: 'plan') . '-' . $plan->id;
            }
            if ($addBillingPeriod || empty($plan->billing_period)) {
                $period = $plan->billing_cycle ?? 'monthly';
                $updates['billing_period'] = in_array($period, ['monthly', 'quarterly', 'yearly'], true)
                    ? $period
                    : 'monthly';
            }
            if ($addActive || $plan->is_active === null) {
                $updates['is_active'] = ($plan->status ?? 'active') === 'active';
            }

            if ($updates !== []) {
                DB::table('subscription_plans')->where('id', $plan->id)->update($updates);
            }
        }

        if ($addSlug) {
            Schema::table('subscription_plans', function (Blueprint $table): void {
                $table->string('slug')->nullable(false)->unique()->change();
            });
        }
        if ($addBillingPeriod) {
            Schema::table('subscription_plans', function (Blueprint $table): void {
                $table->string('billing_period')->default('monthly')->nullable(false)->change();
            });
        }
        if ($addActive) {
            Schema::table('subscription_plans', function (Blueprint $table): void {
                $table->boolean('is_active')->default(true)->nullable(false)->change();
            });
        }
        if (Schema::hasColumn('subscription_plans', 'max_products')) {
            Schema::table('subscription_plans', function (Blueprint $table): void {
                $table->integer('max_products')->nullable()->default(null)->change();
            });
        }

        $legacyColumns = array_values(array_filter(
            ['billing_cycle', 'status'],
            fn (string $column) => Schema::hasColumn('subscription_plans', $column)
        ));
        if ($legacyColumns !== []) {
            Schema::table('subscription_plans', function (Blueprint $table) use ($legacyColumns): void {
                $table->dropColumn($legacyColumns);
            });
        }
    }

    public function down(): void
    {
    }
};
