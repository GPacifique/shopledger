<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Subscription;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '2507881234',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => '1',
            'business_name' => 'Test Business',
            'business_type' => 'retail',
            'country' => 'Rwanda',
            'city' => 'Kigali',
            'address' => 'Test address',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('verification.notice'));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'shop_admin',
        ]);
        $this->assertAuthenticated('web');
        $this->assertDatabaseHas('subscriptions', [
            'shop_id' => auth()->user()->shop_id,
            'status' => 'trial',
        ]);
    }
}
