<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'tournamentCount' => Tournament::query()->count(),
            'userCount' => User::query()->count(),
            'activeTournaments' => Tournament::query()->whereIn('status', ['registration', 'ready', 'live'])->latest()->get(),
        ]);
    }
}
