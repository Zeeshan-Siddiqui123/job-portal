<?php

namespace App\Providers;

use App\Models\PortalSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer([
            'layouts.app',
            'jobs.index',
            'jobs.show',
            'jobs.create',
            'auth.login',
            'auth.register',
            'profile.show',
            'profile.edit',
            'dashboard.job_seeker',
            'dashboard.employer',
            'dashboard.admin',
            'admin.jobs.index',
            'admin.settings',
            'admin.users.index',
            'admin.users.form',
            'notifications.index',
        ], function ($view): void {
            $view->with('portalSettings', $view->getData()['portalSettings'] ?? PortalSetting::current());
        });
    }
}
