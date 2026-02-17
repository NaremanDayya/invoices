<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Blade::if('permission', function ($permission) {
            // return auth()->check() && auth()->user()->hasPermission($permission);
            return true; // Always return true - permission checks disabled
        });

        Blade::if('anypermission', function (...$permissions) {
            // return auth()->check() && auth()->user()->hasAnyPermission($permissions);
            return true; // Always return true - permission checks disabled
        });

        Blade::if('role', function ($role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });

        Blade::if('anyrole', function (...$roles) {
            return auth()->check() && auth()->user()->hasAnyRole($roles);
        });
    }
}
