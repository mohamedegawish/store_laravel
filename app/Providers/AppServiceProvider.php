<?php

namespace App\Providers;

use App\Models\StoreSetting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrap();

        View::composer('*', function ($view) {
            $view->with('settings', StoreSetting::current());
        });
    }
}
