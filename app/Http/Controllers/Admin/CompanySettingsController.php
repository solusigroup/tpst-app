<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CompanySettingsController extends Controller
{
    public function edit()
    {
        Gate::authorize('view_company_settings');
        $tenant = auth()->user()->tenant;
        return view('admin.company-settings', compact('tenant'));
    }

    public function update(Request $request)
    {
        Gate::authorize('update_company_settings');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'bank_name' => 'nullable|string',
            'bank_account_number' => 'nullable|string',
            'bank_account_name' => 'nullable|string',
            'director_name' => 'nullable|string',
            'manager_name' => 'nullable|string',
            'finance_name' => 'nullable|string',
        ]);

        $tenant = auth()->user()->tenant;

        if ($tenant) {
            $tenant->update($validated);
            return redirect()->route('admin.company-settings')->with('success', 'Pengaturan perusahaan berhasil disimpan.');
        }

        return redirect()->route('admin.company-settings')->with('error', 'Tenant tidak ditemukan.');
    }
}
