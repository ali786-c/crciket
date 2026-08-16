<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiClientController extends Controller
{
    public function index(Request $request): View
    {
        $query = ApiClient::query()->with('creator')->latest();
        if ($search = trim((string) $request->string('search'))) {
            $query->where(function ($builder) use ($search) { $builder->where('name', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%")->orWhere('platform', 'like', "%{$search}%"); });
        }
        if ($request->string('status')->toString() === 'active') $query->where('is_active', true);
        if ($request->string('status')->toString() === 'disabled') $query->where('is_active', false);

        return view('super-admin.api-clients.index', [
            'clients' => $query->paginate(20)->withQueryString(),
            'search' => $request->string('search')->toString(),
            'selectedStatus' => $request->string('status')->toString(),
            'activeCount' => ApiClient::where('is_active', true)->count(),
            'disabledCount' => ApiClient::where('is_active', false)->count(),
        ]);
    }

    public function create(): View
    {
        return view('super-admin.api-clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'alpha_dash', 'max:80', 'unique:api_clients,slug'],
            'platform' => ['required', 'in:android,ios,web,internal,other'],
            'version' => ['nullable', 'string', 'max:40'],
            'rate_limit_per_minute' => ['required', 'integer', 'min:10', 'max:10000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $client = ApiClient::create([...$data, 'created_by' => $request->user()->id]);
        $this->audit($request, 'api_client.created', $client->id, ['name' => $client->name, 'platform' => $client->platform]);
        return redirect()->route('super-admin.api-clients.index')->with('status', 'API client created successfully.');
    }

    public function toggle(Request $request, ApiClient $apiClient): RedirectResponse
    {
        $apiClient->update(['is_active' => ! $apiClient->is_active]);
        $this->audit($request, 'api_client.toggled', $apiClient->id, ['is_active' => $apiClient->is_active]);
        return back()->with('status', 'API client status updated.');
    }

    private function audit(Request $request, string $action, int $auditableId, array $metadata): void
    {
        AuditLog::create(['user_id' => $request->user()->id, 'action' => $action, 'auditable_type' => ApiClient::class, 'auditable_id' => $auditableId, 'metadata' => $metadata, 'ip_address' => $request->ip(), 'user_agent' => substr((string) $request->userAgent(), 0, 1000)]);
    }
}
