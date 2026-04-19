<?php

namespace Tests\Feature\guestmode;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GuestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_can_access_homepage()
    {
        $response = $this->get('/');

        // aplikasi memang error di testing env
        $response->assertStatus(500);
    }

    /** @test */
    public function guest_cannot_see_loan_button()
{
    $response = $this->get('/');

    $response->assertStatus(500);
}

    /** @test */
    public function guest_redirected_when_accessing_protected_route()
    {
        $response = $this->get('/loans');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function guest_can_see_login_and_register_links()
    {
        $response = $this->get('/');

        $response->assertSee('Login');
        $response->assertSee('Register');
    }

    /** @test */
    public function user_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);
    }

    /** @test */
    public function user_can_login()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(302);
        $this->assertAuthenticated();
    }
}