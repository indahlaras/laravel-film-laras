<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Genre;
use App\Models\Kritik;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
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

        Schema::create('genres', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('films', function (Blueprint $table): void {
            $table->id();
            $table->text('title');
            $table->text('sinopsis');
            $table->integer('year');
            $table->text('poster');
            $table->foreignId('genre_id');
            $table->timestamps();
        });

        Schema::create('casts', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->integer('age');
            $table->text('bio');
            $table->timestamps();
        });

        Schema::create('perans', function (Blueprint $table): void {
            $table->id();
            $table->string('actor');
            $table->foreignId('film_id');
            $table->foreignId('cast_id');
            $table->timestamps();
        });

        Schema::create('kritiks', function (Blueprint $table): void {
            $table->id();
            $table->text('comment');
            $table->integer('rating');
            $table->foreignId('user_id');
            $table->foreignId('film_id');
            $table->timestamps();
        });

        Role::create(['name' => 'user']);
        Role::create(['name' => 'admin']);
    }

    public function test_admin_can_log_in_with_username_and_password_and_reaches_admin_dashboard(): void
    {
        $admin = $this->createUser('admin', 2);
        $genre = Genre::create(['name' => 'Drama']);
        Film::create([
            'title' => 'Film Contoh',
            'sinopsis' => 'Sinopsis film.',
            'year' => 2025,
            'poster' => 'film-contoh.jpg',
            'genre_id' => $genre->id,
        ]);
        $film = Film::firstOrFail();
        $this->createComment($this->createUser('member', 1), $film);

        $response = $this->post(route('login.store'), [
            'username' => 'admin',
            'password' => 'secret-password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Selamat datang, admin!')
            ->assertSee('Film Contoh')
            ->assertSee('Drama')
            ->assertSee('Moderasi komentar')
            ->assertSee('member')
            ->assertSee('Komentar dummy');
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

    public function test_guest_cannot_submit_a_comment(): void
    {
        $film = $this->createFilm();

        $this->post(route('comments.store', $film), [
            'comment' => 'Komentar tanpa login',
            'rating' => 4,
        ])->assertRedirect(route('login'));

        $this->assertDatabaseCount('kritiks', 0);
    }

    public function test_authenticated_user_can_create_a_comment_and_name_is_displayed(): void
    {
        $user = $this->createUser('reviewer', 1);
        $film = $this->createFilm();

        $this->actingAs($user)->post(route('comments.store', $film), [
            'comment' => 'Film yang sangat menarik.',
            'rating' => 5,
        ])->assertRedirect(route('movies.show', $film));

        $comment = Kritik::firstOrFail();
        $this->assertSame($user->id, $comment->user_id);
        $this->assertSame($film->id, $comment->film_id);
        $this->get(route('movies.show', $film))
            ->assertOk()
            ->assertSee('reviewer')
            ->assertSee('Film yang sangat menarik.');
    }

    public function test_comment_owner_can_update_and_delete_their_comment(): void
    {
        $user = $this->createUser('reviewer', 1);
        $comment = $this->createComment($user, $this->createFilm());

        $this->actingAs($user)->put(route('comments.update', $comment), [
            'comment' => 'Komentar yang sudah diperbarui.',
            'rating' => 3,
        ])->assertRedirect(route('movies.show', $comment->film));

        $this->assertDatabaseHas('kritiks', [
            'id' => $comment->id,
            'comment' => 'Komentar yang sudah diperbarui.',
            'rating' => 3,
        ]);

        $this->delete(route('comments.destroy', $comment))
            ->assertRedirect(route('movies.show', $comment->film));

        $this->assertDatabaseMissing('kritiks', ['id' => $comment->id]);
    }

    public function test_user_cannot_update_or_delete_another_users_comment(): void
    {
        $owner = $this->createUser('owner', 1);
        $otherUser = $this->createUser('other-user', 1);
        $comment = $this->createComment($owner, $this->createFilm());

        $this->actingAs($otherUser)
            ->put(route('comments.update', $comment), [
                'comment' => 'Perubahan tidak diizinkan.',
                'rating' => 1,
            ])
            ->assertForbidden();

        $this->delete(route('comments.destroy', $comment))->assertForbidden();
        $this->assertDatabaseHas('kritiks', ['id' => $comment->id, 'comment' => 'Komentar dummy']);
    }

    public function test_admin_can_moderate_comments_from_the_dashboard(): void
    {
        $admin = $this->createUser('admin-moderator', 2);
        $comment = $this->createComment($this->createUser('reviewer', 1), $this->createFilm());

        $this->actingAs($admin)
            ->delete(route('admin.comments.destroy', $comment))
            ->assertRedirect(route('admin.dashboard').'#komentar');

        $this->assertDatabaseMissing('kritiks', ['id' => $comment->id]);
    }

    private function createFilm(): Film
    {
        $genre = Genre::firstOrCreate(['name' => 'Drama']);

        return Film::create([
            'title' => 'Film Contoh',
            'sinopsis' => 'Sinopsis film.',
            'year' => 2025,
            'poster' => 'film-contoh.jpg',
            'genre_id' => $genre->id,
        ]);
    }

    private function createComment(User $user, Film $film): Kritik
    {
        return Kritik::create([
            'comment' => 'Komentar dummy',
            'rating' => 4,
            'user_id' => $user->id,
            'film_id' => $film->id,
        ]);
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
