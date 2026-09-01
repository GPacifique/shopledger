<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SubscriptionPlanModelTest extends TestCase
{
    public function test_subscription_plan_model_class_is_resolvable(): void
    {
        $this->assertTrue(class_exists(\App\Models\SubscriptionPlan::class));
    }
}
