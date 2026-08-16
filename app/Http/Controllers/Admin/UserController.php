<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()->with('roles')->latest()->paginate(20),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        $user->assignRole(Role::findOrCreate('captain', 'web'));

        return back()->with('status', 'Captain account created successfully.');
    }

    public function promoteCaptain(User $user): RedirectResponse
    {
        abort_if($user->hasRole('admin'), 422, 'An administrator cannot be changed into a captain.');

        $user->removeRole('player');
        $user->assignRole(Role::findOrCreate('captain', 'web'));

        return back()->with('status', "{$user->name} is now a captain.");
    }

    public function revokeCaptain(User $user): RedirectResponse
    {
        abort_if($user->hasRole('admin'), 422, 'An administrator cannot be revoked as a captain.');

        DB::transaction(function () use ($user): void {
            $user->teamCaptainAssignments()->whereNull('revoked_at')->update(['revoked_at' => now()]);
            $user->removeRole('captain');
            if ($user->getRoleNames()->isEmpty()) {
                $user->assignRole(Role::findOrCreate('player', 'web'));
            }
        });

        return back()->with('status', "Captain role revoked from {$user->name}.");
    }
}
