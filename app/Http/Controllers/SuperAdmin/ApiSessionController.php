<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Sanctum\PersonalAccessToken;

class ApiSessionController extends Controller
{
    public function index(Request $request): View
    {
        $query = PersonalAccessToken::query()->with('tokenable')->latest();
        if ($search = trim((string) $request->string('search'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhereHasMorph('tokenable', [User::class], fn ($user) => $user->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }
        if ($request->string('status')->toString() === 'expired') {
            $query->whereNotNull('expires_at')->where('expires_at', '<=', now());
        } elseif ($request->string('status')->toString() === 'active') {
            $query->where(function ($builder) { $builder->whereNull('expires_at')->orWhere('expires_at', '>', now()); });
        }

        $now = now();
        return view('super-admin.api-sessions.index', [
            'sessions' => $query->paginate(30)->withQueryString(),
            'search' => $request->string('search')->toString(),
            'selectedStatus' => $request->string('status')->toString(),
            'activeCount' => PersonalAccessToken::where(function ($builder) use ($now) { $builder->whereNull('expires_at')->orWhere('expires_at', '>', $now); })->count(),
            'expiredCount' => PersonalAccessToken::whereNotNull('expires_at')->where('expires_at', '<=', $now)->count(),
        ]);
    }

    public function revoke(Request $request, PersonalAccessToken $token): RedirectResponse
    {
        $tokenId = $token->id;
        $token->delete();
        AuditLog::create(['user_id' => $request->user()->id, 'action' => 'api_session.revoked', 'auditable_type' => PersonalAccessToken::class, 'auditable_id' => $tokenId, 'metadata' => ['reason' => 'super_admin_action'], 'ip_address' => $request->ip(), 'user_agent' => substr((string) $request->userAgent(), 0, 1000)]);
        return back()->with('status', 'API session revoked.');
    }

    public function revokeExpired(Request $request): RedirectResponse
    {
        $tokens = PersonalAccessToken::query()->whereNotNull('expires_at')->where('expires_at', '<=', now());
        $count = $tokens->count();
        $tokens->delete();
        AuditLog::create(['user_id' => $request->user()->id, 'action' => 'api_sessions.expired_revoked', 'auditable_type' => PersonalAccessToken::class, 'metadata' => ['token_count' => $count], 'ip_address' => $request->ip(), 'user_agent' => substr((string) $request->userAgent(), 0, 1000)]);
        return back()->with('status', $count.' expired API session(s) revoked.');
    }
}
