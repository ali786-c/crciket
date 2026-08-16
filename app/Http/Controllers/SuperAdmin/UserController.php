<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()
            ->with('roles')
            ->withCount(['tokens', 'auditLogs'])
            ->latest();

        if ($search = trim((string) $request->string('search'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->string('role')->toString()) {
            $query->role($role);
        }

        return view('super-admin.users.index', [
            'users' => $query->paginate(25)->withQueryString(),
            'search' => $request->string('search')->toString(),
            'selectedRole' => $request->string('role')->toString(),
            'roleCounts' => collect(['super_admin', 'admin', 'captain', 'player'])
                ->mapWithKeys(fn (string $name) => [$name => User::role($name)->count()]),
        ]);
    }

    public function show(User $user): View
    {
        $user->load('roles', 'playerProfile');
        $user->loadCount(['tokens', 'auditLogs', 'reviewedTournamentPlayers', 'selectedDraftPicks']);

        return view('super-admin.users.show', [
            'user' => $user,
            'sessions' => $user->tokens()->latest()->take(20)->get(),
            'auditLogs' => $user->auditLogs()->with('tournament')->latest()->take(20)->get(),
        ]);
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['role' => ['required', 'in:admin,captain,player,super_admin']]);
        $newRole = $data['role'];
        $oldRole = $user->getRoleNames()->first();

        if ($oldRole === $newRole) {
            return back()->with('status', 'User role is already set to '.$newRole.'.');
        }

        if ($user->is($request->user()) && $newRole !== 'super_admin') {
            return back()->withErrors(['role' => 'You cannot remove your own Super Admin access.']);
        }

        if ($oldRole === 'super_admin' && $newRole !== 'super_admin' && User::role('super_admin')->count() <= 1) {
            return back()->withErrors(['role' => 'The platform must retain at least one Super Admin.']);
        }

        DB::transaction(function () use ($request, $user, $oldRole, $newRole): void {
            $user->syncRoles([$newRole]);
            $this->audit($request, 'super_admin.user_role_changed', $user->id, [
                'before' => ['role' => $oldRole],
                'after' => ['role' => $newRole],
            ]);
        });

        return back()->with('status', 'User role updated successfully.');
    }

    public function revokeSessions(Request $request, User $user): RedirectResponse
    {
        $count = $user->tokens()->count();
        $user->tokens()->delete();
        $this->audit($request, 'super_admin.user_sessions_revoked', $user->id, ['token_count' => $count]);

        return back()->with('status', $count.' API session(s) revoked.');
    }

    private function audit(Request $request, string $action, int $auditableId, array $metadata): void
    {
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => $action,
            'auditable_type' => User::class,
            'auditable_id' => $auditableId,
            'metadata' => $metadata,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 1000),
        ]);
    }
}
