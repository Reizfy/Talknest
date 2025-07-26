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
                $user = auth()->user();
                $canCreate = $user ? true : false;
                $nests = collect();
                if ($user) {
                    $nests = $user->nests()->select('nests.id', 'nests.name', 'nests.profile_image')->get();
                }
                $view->with([
                    'nests' => $nests,
                    'canCreate' => $canCreate,
                    'user' => $user
                ]);
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
