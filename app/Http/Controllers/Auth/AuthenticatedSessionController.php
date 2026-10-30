<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([
            'name' => $credentials['username'],
            'password' => $credentials['password'],
        ])) {
            throw ValidationException::withMessages([
                'username' => 'Username atau password salah.',
            ]);
        }

        $request->session()->regenerate();

        if (strtolower($request->user()->role?->name ?? '') === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('home');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,name'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'umur' => ['required', 'integer', 'min:1', 'max:120'],
            'bio' => ['required', 'string', 'max:500'],
            'alamat' => ['required', 'string', 'max:1000'],
        ]);

        $userRole = Role::where('name', 'user')->firstOrFail();

        $user = DB::transaction(function () use ($validated, $userRole): User {
            $profile = Profile::create([
                'umur' => $validated['umur'],
                'bio' => $validated['bio'],
                'alamat' => $validated['alamat'],
            ]);

            return User::create([
                'name' => $validated['username'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'profile_id' => $profile->id,
                'role_id' => $userRole->id,
            ]);
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
