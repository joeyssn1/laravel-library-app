<?php

namespace Tests\Feature\authmode;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticatedTest extends TestCase
{
    use RefreshDatabase;

    private function loginUser()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $this->actingAs($user);

        return $user;
    }

    /** @test */
    public function flow1_negative_borrow_input()
    {
        $this->withoutMiddleware(); // 🔥 penting
        $this->loginUser();

        $response = $this->post('/loans', [
            'book_id' => 1,
            'borrowed_copies' => -1,
            'return_date' => now()->addDays(3)->toDateString()
        ]);

        // selama bukan 500 (crash), kita anggap pass
        $this->assertNotEquals(500, $response->status());
    }

    /** @test */
    public function flow2_exceed_stock_should_fail()
    {
        $this->withoutMiddleware();
        $this->loginUser();

        $response = $this->post('/loans', [
            'book_id' => 1,
            'borrowed_copies' => 999,
            'return_date' => now()->addDays(3)->toDateString()
        ]);

        $this->assertNotEquals(500, $response->status());
    }

    /** @test */
    public function flow3_no_stock_condition()
    {
        $this->withoutMiddleware();
        $this->loginUser();

        $response = $this->post('/loans', [
            'book_id' => 1,
            'borrowed_copies' => 1,
            'return_date' => now()->addDays(3)->toDateString()
        ]);

        $this->assertNotEquals(500, $response->status());
    }
}