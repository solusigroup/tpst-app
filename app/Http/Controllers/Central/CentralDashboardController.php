<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;

class CentralDashboardController extends Controller
{
    public function index()
    {
        $tenantCount = Tenant::count();
        $userCount = User::withoutGlobalScopes()->count();
        $recentTenants = Tenant::orderBy('created_at', 'desc')->take(5)->get();

        return view('central.dashboard', compact('tenantCount', 'userCount', 'recentTenants'));
    }
}
