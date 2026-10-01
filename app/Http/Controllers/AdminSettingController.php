<?php

namespace App\Http\Controllers;

use App\Models\PortalSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings', ['settings' => PortalSetting::current()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => 'required|string|max:80',
            'support_email' => 'nullable|email|max:150',
            'registration_open' => 'required|boolean',
            'job_posting_open' => 'required|boolean',
            'applications_open' => 'required|boolean',
        ]);
        $settings = PortalSetting::current();
        $settings->fill($data)->save();

        return back()->with('success', 'System settings updated.');
    }
}
