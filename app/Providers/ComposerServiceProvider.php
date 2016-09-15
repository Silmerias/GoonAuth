<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use View;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', \App\Http\ViewComposers\AuthenticatedComposer::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
