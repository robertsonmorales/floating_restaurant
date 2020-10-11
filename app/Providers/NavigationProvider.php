<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;

class NavigationProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $navigations = file_get_contents(public_path('js/navigation.json'));
        View::share('navigations', json_decode($navigations, true));
        
        // $navigations = Storage::disk('local')->get('navigations.json');
        // View::share('navigations', json_decode($navigations, true));
    }
}
