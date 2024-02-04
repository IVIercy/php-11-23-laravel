<?php

namespace Tests\Unit\Services;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthRegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register_with_valid_data()
    {
        $response = $this->post('/register', [
            'name' => 'Tom',
            'surname' => 'Cruise',
            'email' => 'tom@example.com',
            'phone' => '123456789',
            'birthdate' => '1990-01-01',
            'password' => 'test1234',
            'password_confirmation' => 'test1234',
        ]);

        $response->assertRedirect('/home');
        $this->assertDatabaseHas('users', ['email' => 'tom@example.com']);
    }

    /** @test */
    public function user_cannot_register_with_invalid_data()
    {
        $response = $this->post('/register', [
        ]);

        $response->assertSessionHasErrors(['name', 'surname', 'email', 'phone', 'birthdate', 'password']);
        $this->assertDatabaseCount('users', 0);
    }

    /** @test */
    public function user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'test1234',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_cannot_login_with_incorrect_credentials()
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function admin_can_access_admin_dashboard()
    {
        $admin = User::factory()->create();

        $adminRole = 'admin';

        $admin->assignRole($adminRole);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
    }

    /** @test */
    public function non_admin_cannot_access_admin_dashboard()
    {
        $user = User::factory()->create();

        $userRole = 'customer';

        $user->assignRole($userRole);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertForbidden();
    }

    protected function getUser(Roles $role = Roles::ADMIN): User
    {
        return User::role($role->value)->firstOrFail();
    }
}
