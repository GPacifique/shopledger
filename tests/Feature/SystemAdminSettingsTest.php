<?php

namespace Tests\Feature;

use App\Models\Settings;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemAdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_admin_can_view_and_update_settings_using_put(): void
    {
        $user = User::factory()->create(['role' => 'system_admin']);

        $this->actingAs($user)
            ->get(route('admin.settings'))
            ->assertOk()
            ->assertSee('MahWi')
            ->assertSee('RWF');

        $this->actingAs($user)
            ->put(route('admin.settings.update'), [
                'app_name' => 'Mahwi POS',
                'default_locale' => 'en',
                'currency' => 'RWF',
                'support_email' => 'support@example.com',
                'support_phone' => '0786163963',
                'monthly_price' => '30000',
                'trial_days' => '15',
                'registration_open' => '1',
                'maintenance_mode' => '1',
                'maintenance_message' => 'Scheduled maintenance.',
            ])
            ->assertRedirect(route('admin.settings'))
            ->assertSessionHas('success');

        $this->assertSame('Mahwi POS', Settings::get('app_name'));
        $this->assertSame('1', Settings::get('maintenance_mode'));
        $this->assertSame('Scheduled maintenance.', Settings::get('maintenance_message'));
    }
}
