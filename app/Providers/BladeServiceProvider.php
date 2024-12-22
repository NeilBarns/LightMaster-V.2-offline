<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;

class BladeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blade::directive('can', function ($permissions) {
            return "<?php if(Auth::check() && collect({$permissions})->some(fn(\$permission) => Auth::user()->hasPermission(\$permission))): ?>";
        });

        Blade::directive('endcan', function () {
            return "<?php endif; ?>";
        });
    }

    public function register()
    {
        //
    }
}
