<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase as BaseTestCase;

class AuthenticationTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => false,
        ]);
        DB::purge('sqlite');

        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignId('profile_id');
            $table->foreignId('role_id');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('profiles', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('umur');
            $table->text('bio');
            $table->text('alamat');
            $table->timestamps();
        });

        Role::create(['name' => 'user']);
        Role::create(['name' => 'admin']);
    }

    public function test_admin_can_log_in_with_username_and_password_and_reaches_admin_dashboard(): void
    {
        $admin = $this->createUser('admin', 2);

        $response = $this->post(route('login.store'), [
            'username' => 'admin',
            'password' => 'secret-password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->get(route('admin.dashboard'))->assertOk();
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        $this->createUser('admin', 2);

        $this->from('/')->post(route('login.store'), [
            'username' => 'admin',
            'password' => 'wrong-password',
        ])->assertRedirect('/')
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_non_admin_cannot_open_admin_dashboard(): void
    {
        $user = $this->createUser('member', 1);

        $this->post(route('login.store'), [
            'username' => 'member',
            'password' => 'secret-password',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
        $this->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_registration_creates_a_user_profile_and_signs_in_with_user_role(): void
    {
        $response = $this->post(route('register.store'), [
            'auth_form' => 'register',
            'username' => 'new-member',
            'email' => 'new-member@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
            'umur' => 20,
            'bio' => 'Penggemar film',
            'alamat' => 'Jakarta',
        ]);

        $response->assertRedirect(route('home'));

        $user = User::where('name', 'new-member')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame('user', $user->role->name);
        $this->assertSame(20, $user->profile->umur);
        $this->assertTrue(Hash::check('secret-password', $user->password));
    }

    public function test_registration_rejects_duplicate_usernames_and_mismatched_passwords(): void
    {
        $this->createUser('existing-member', 1);

        $this->from('/')->post(route('register.store'), [
            'auth_form' => 'register',
            'username' => 'existing-member',
            'email' => 'another@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'different-password',
            'umur' => 20,
            'bio' => 'Penggemar film',
            'alamat' => 'Jakarta',
        ])->assertRedirect('/')
            ->assertSessionHasErrors(['username', 'password']);

        $this->assertGuest();
    }

    private function createUser(string $username, int $roleId): User
    {
        $profile = Profile::create([
            'umur' => 20,
            'bio' => '',
            'alamat' => '',
        ]);

        return User::create([
            'name' => $username,
            'email' => "{$username}@example.com",
            'password' => Hash::make('secret-password'),
            'profile_id' => $profile->id,
            'role_id' => $roleId,
        ]);
    }
}
