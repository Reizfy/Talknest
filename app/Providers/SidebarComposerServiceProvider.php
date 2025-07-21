<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Nest;

class SidebarComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        View::composer(
            'partials.dashboard.vertical-nav',
            function ($view) {
                $view->with('nests', Nest::select('id', 'name', 'title', 'banner')->get());
            }
        );
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        //
    }
}
