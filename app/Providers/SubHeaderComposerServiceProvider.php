<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use App\Models\Nest;

class SubHeaderComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('partials.dashboard.sub-header', function ($view) {
            $nest = null;
            $route = Route::current();
            if ($route && $route->parameter('name')) {
                $nest = Nest::where('name', $route->parameter('name'))->first();
            }
            $view->with('nest', $nest);
        });
    }

    public function register()
    {
        //
    }
}
