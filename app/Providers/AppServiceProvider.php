<?php

namespace App\Providers;

use App\Policies\RolePolicy;
use Illuminate\Support\ServiceProvider;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

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
        Page::formActionsAlignment(Alignment::Right);
        Gate::policy(Role::class, RolePolicy::class);
    }
}
