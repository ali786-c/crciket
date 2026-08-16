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

    public function exportCaptains(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $captains = User::role('captain')->get();
        $captainsData = [];

        foreach ($captains as $captain) {
            $newPassword = 'CD-' . rand(10000, 99999);
            $captain->update([
                'password' => Hash::make($newPassword)
            ]);
            $captainsData[] = [
                'name' => $captain->name,
                'email' => $captain->email,
                'password' => $newPassword,
            ];
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="captain_credentials.csv"',
        ];

        $callback = function () use ($captainsData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Email', 'Password']);

            foreach ($captainsData as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', "Password for {$user->name} updated successfully.");
    }
}
