<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_berhasil_dengan_kredensial_valid(): void
    {
        $user = User::factory()->create([
            'username'  => 'testuser',
            'password'  => bcrypt('Password1'),
            'is_active' => true,
        ]);

        $response = $this->post(route('login.post'), [
            'username' => 'testuser',
            'password' => 'Password1',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_gagal_dengan_password_salah(): void
    {
        User::factory()->create([
            'username' => 'testuser',
            'password' => bcrypt('Password1'),
        ]);

        $this->post(route('login.post'), [
            'username' => 'testuser',
            'password' => 'salah123',
        ])->assertSessionHasErrors();
    }

    public function test_user_nonaktif_tidak_bisa_login(): void
    {
        User::factory()->create([
            'username'  => 'nonaktif',
            'password'  => bcrypt('Password1'),
            'is_active' => false,
        ]);

        $this->post(route('login.post'), [
            'username' => 'nonaktif',
            'password' => 'Password1',
        ])->assertSessionHasErrors();
    }
}
