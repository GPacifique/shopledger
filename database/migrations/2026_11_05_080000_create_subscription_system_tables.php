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
        $userColumns = [];
        foreach (['phone', 'country', 'city', 'address'] as $column) {
            if (! Schema::hasColumn('users', $column)) {
                $userColumns[] = $column;
            }
        }

        if ($userColumns !== []) {
            Schema::table('users', function (Blueprint $table) use ($userColumns): void {
                foreach ($userColumns as $column) {
                    $table->string($column)->nullable();
                }
                if (in_array('phone', $userColumns, true)) {
                    $table->unique('phone');
                }
            });
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->enum('role', [
                'system_admin',
                'shop_admin',
                'manager',
                'seller',
                'accountant',
                'cashier',
                'waiter',
                'user',
            ])->default('user')->change();
        });

        if (! Schema::hasTable('subscription_plans')) {
            Schema::create('subscription_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->decimal('price', 12, 2);
                $table->enum('billing_period', ['monthly', 'quarterly', 'yearly'])->default('monthly');
                $table->unsignedInteger('max_users')->default(1);
                $table->unsignedInteger('max_products')->nullable();
                $table->json('features')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        } else {
            $this->upgradeSubscriptionPlansTable();
        }

        if (DB::table('subscription_plans')->count() === 0) {
            DB::table('subscription_plans')->insert([
                'name' => 'Basic',
                'slug' => 'basic',
                'price' => 10000,
                'billing_period' => 'monthly',
                'max_users' => 3,
                'max_products' => 100,
                'features' => json_encode([]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->constrained('subscription_plans')->restrictOnDelete();
            $table->enum('status', ['trial', 'active', 'past_due', 'expired', 'cancelled', 'suspended'])->default('trial');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('grace_ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->timestamps();
            $table->index(['status', 'ends_at']);
        });

        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->enum('method', ['mobile_money', 'bank', 'cash']);
            $table->string('reference')->nullable();
            $table->timestamp('paid_at');
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['shop_id', 'paid_at']);
        });

        $fallbackPlanId = DB::table('subscription_plans')->orderBy('id')->value('id');
        foreach (DB::table('shops')->orderBy('id')->get() as $shop) {
            $legacySubscription = Schema::hasTable('shopsubscriptions')
                ? DB::table('shopsubscriptions')->where('shop_id', $shop->id)->orderByDesc('id')->first()
                : null;
            $planId = $legacySubscription->subscriptionplan_id
                ?? ($shop->subscriptionplan_id ?? null);
            $planId = DB::table('subscription_plans')->where('id', $planId)->value('id')
                ?? $fallbackPlanId;

            $pending = ($shop->status ?? null) === 'pending';
            $startsAt = now();
            $endsAt = $pending
                ? now()->addDays((int) config('subscription.trial_days', 14))
                : now()->addMonth();

            if (! empty($legacySubscription->end_date) && ! $pending) {
                $legacyEnd = \Illuminate\Support\Carbon::parse($legacySubscription->end_date);
                if ($legacyEnd->isFuture()) {
                    $endsAt = $legacyEnd;
                }
            }

            DB::table('subscriptions')->insert([
                'shop_id' => $shop->id,
                'subscription_plan_id' => $planId,
                'status' => $pending ? 'trial' : (($shop->status ?? null) === 'approved' ? 'active' : 'expired'),
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'trial_ends_at' => $pending ? $endsAt : null,
                'auto_renew' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
        Schema::dropIfExists('subscriptions');
    }

    private function upgradeSubscriptionPlansTable(): void
    {
        $addSlug = ! Schema::hasColumn('subscription_plans', 'slug');
        $addBillingPeriod = ! Schema::hasColumn('subscription_plans', 'billing_period');
        $addActive = ! Schema::hasColumn('subscription_plans', 'is_active');

        Schema::table('subscription_plans', function (Blueprint $table) use ($addSlug, $addBillingPeriod, $addActive): void {
            if ($addSlug) {
                $table->string('slug')->nullable()->unique();
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
                $slug = Str::slug($plan->name ?? '') ?: 'plan';
                $updates['slug'] = $slug . '-' . $plan->id;
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

        Schema::table('subscription_plans', function (Blueprint $table) use ($addSlug, $addBillingPeriod, $addActive): void {
            if ($addSlug) {
                $table->string('slug')->nullable(false)->change();
            }
            if ($addBillingPeriod) {
                $table->string('billing_period')->default('monthly')->nullable(false)->change();
            }
            if ($addActive) {
                $table->boolean('is_active')->default(true)->nullable(false)->change();
            }
            $table->integer('max_products')->nullable()->default(null)->change();
        });

        $dropLegacyColumns = array_values(array_filter(
            ['billing_cycle', 'status'],
            fn (string $column) => Schema::hasColumn('subscription_plans', $column)
        ));
        if ($dropLegacyColumns !== []) {
            Schema::table('subscription_plans', function (Blueprint $table) use ($dropLegacyColumns): void {
                $table->dropColumn($dropLegacyColumns);
            });
        }
    }
};
