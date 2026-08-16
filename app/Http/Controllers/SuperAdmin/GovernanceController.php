<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class GovernanceController extends Controller
{
    public function auditLogs(Request $request): View
    {
        $query = $this->filteredAuditQuery($request);

        return view('super-admin.audit-logs.index', [
            'logs' => $query->paginate(30)->withQueryString(),
            'actions' => AuditLog::query()->select('action')->whereNotNull('action')->distinct()->orderBy('action')->pluck('action'),
            'selectedAction' => $request->string('action')->toString(),
            'selectedUser' => $request->integer('user_id') ?: null,
            'search' => $request->string('search')->toString(),
            'from' => $request->string('from')->toString(),
            'to' => $request->string('to')->toString(),
        ]);
    }

    public function exportAuditLogs(Request $request): StreamedResponse
    {
        $logs = $this->filteredAuditQuery($request)->limit(5000)->get();
        $filename = 'audit-logs-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($logs): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['timestamp', 'action', 'actor', 'actor_email', 'tournament', 'scope', 'ip_address', 'metadata']);
            foreach ($logs as $log) {
                fputcsv($handle, [
                    optional($log->created_at)->toIso8601String(),
                    $log->action,
                    $log->user?->name ?? 'System',
                    $log->user?->email,
                    $log->tournament?->name,
                    $log->auditable_type ? class_basename($log->auditable_type).' #'.$log->auditable_id : 'Platform',
                    $log->ip_address,
                    $log->metadata ? json_encode($log->metadata, JSON_UNESCAPED_SLASHES) : null,
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function health(): View
    {
        $checks = [];
        $started = microtime(true);
        try {
            DB::connection()->getPdo();
            DB::select('SELECT 1');
            $checks['database'] = $this->check('Database', 'healthy', round((microtime(true) - $started) * 1000).' ms response time', 'MySQL connection and query are responding.');
        } catch (\Throwable $exception) {
            $checks['database'] = $this->check('Database', 'critical', 'Unavailable', $exception->getMessage());
        }

        $storageWritable = is_writable(storage_path());
        $freeBytes = @disk_free_space(storage_path());
        $checks['storage'] = $this->check('Storage', $storageWritable ? 'healthy' : 'critical', $storageWritable ? $this->formatBytes($freeBytes) .' free' : 'Not writable', $storageWritable ? 'Laravel storage is writable.' : 'Check filesystem ownership and permissions.');

        $failedJobs = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : null;
        $queuedJobs = Schema::hasTable('jobs') ? DB::table('jobs')->count() : null;
        $checks['queue'] = $this->check('Queue', $failedJobs === null || $failedJobs === 0 ? 'healthy' : 'warning', ($queuedJobs ?? 0).' queued · '.($failedJobs ?? 0).' failed', $failedJobs ? 'Review failed jobs before production operation.' : 'No failed jobs are currently recorded.');

        $checks['api'] = $this->check('Versioned API', Route::has('api.v1.auth.login') ? 'healthy' : 'critical', Route::has('api.v1.auth.login') ? 'v1 routes registered' : 'Route missing', 'Sanctum API route registration check.');
        $checks['application'] = $this->check('Application', app()->isDownForMaintenance() ? 'critical' : 'healthy', app()->environment().' · '.app()->version(), app()->isDownForMaintenance() ? 'Application is in maintenance mode.' : 'Application is serving requests.');
        $checks['security'] = $this->check('Security posture', config('app.debug') ? 'warning' : 'healthy', config('app.debug') ? 'Debug mode enabled' : 'Debug mode disabled', config('app.debug') ? 'Disable APP_DEBUG in production.' : 'Production debug setting is safe.');
        $checks['scheduler'] = $this->check('Scheduler', 'warning', 'Manual verification required', 'Configure the Laravel scheduler or cron on the production host.');

        return view('super-admin.health', [
            'checks' => $checks,
            'environment' => [
                'app_url' => config('app.url'),
                'php' => PHP_VERSION,
                'laravel' => app()->version(),
                'timezone' => config('app.timezone'),
                'debug' => config('app.debug'),
                'maintenance' => app()->isDownForMaintenance(),
            ],
            'checkedAt' => now(),
        ]);
    }

    private function filteredAuditQuery(Request $request)
    {
        $query = AuditLog::query()->with(['user', 'tournament'])->latest();

        if ($search = trim((string) $request->string('search'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('action', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('tournament', fn ($tournament) => $tournament->where('name', 'like', "%{$search}%"));
            });
        }

        if ($action = $request->string('action')->toString()) {
            $query->where('action', $action);
        }
        if ($userId = $request->integer('user_id')) {
            $query->where('user_id', $userId);
        }
        if ($from = $request->date('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->date('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }

    private function check(string $label, string $status, string $value, string $detail): array
    {
        return compact('label', 'status', 'value', 'detail');
    }

    private function formatBytes(?float $bytes): string
    {
        if ($bytes === null) {
            return 'Unknown free space';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $index = 0;
        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }
        return round($bytes, 1).' '.$units[$index];
    }
}
