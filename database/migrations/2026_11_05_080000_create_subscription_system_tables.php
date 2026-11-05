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

        if (Schema::hasTable('subscriptionplans')) {
            foreach (DB::table('subscriptionplans')->orderBy('id')->get() as $legacyPlan) {
                $name = $legacyPlan->name ?: 'Plan ' . $legacyPlan->id;
                $slug = Str::slug($name) ?: 'plan-' . $legacyPlan->id;

                DB::table('subscription_plans')->insert([
                    'id' => $legacyPlan->id,
                    'name' => $name,
                    'slug' => $slug . '-' . $legacyPlan->id,
                    'price' => $legacyPlan->price ?? 0,
                    'billing_period' => in_array($legacyPlan->billing_cycle ?? null, ['monthly', 'quarterly', 'yearly'], true)
                        ? $legacyPlan->billing_cycle
                        : 'monthly',
                    'max_users' => max((int) ($legacyPlan->max_users ?? 1), 1),
                    'max_products' => isset($legacyPlan->max_products) && (int) $legacyPlan->max_products > 0
                        ? $legacyPlan->max_products
                        : null,
                    'features' => $legacyPlan->features ?? null,
                    'is_active' => ($legacyPlan->status ?? 'active') === 'active',
                    'created_at' => $legacyPlan->created_at ?? now(),
                    'updated_at' => $legacyPlan->updated_at ?? now(),
                ]);
            }
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
        Schema::dropIfExists('subscription_plans');
    }
};
