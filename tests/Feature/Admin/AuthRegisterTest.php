<?php

namespace Tests\Feature\Admin;

use App\Enums\Roles;
use App\Models\User;
use Database\Seeders\PermissionAndRolesSeeder;
use Database\Seeders\UsersSeeder;
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
        $this->assertDatabaseMissing('users', ['email' => 'john@example.com']);
    }

    /** @test */
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
    protected function afterRefreshingDatabase()
    {
        $this->seed(PermissionAndRolesSeeder::class);
        $this->seed(UsersSeeder::class);
    }



    protected function getUser(Roles $role = Roles::ADMIN):User
    {
        return User::role($role->value)->firstOrFail();

    }
}
