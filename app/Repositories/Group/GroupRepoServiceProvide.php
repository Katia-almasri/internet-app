<?php

namespace App\Repositories\Group;


use Illuminate\Support\ServiceProvider;


class GroupRepoServiceProvide extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        
    }


    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Repositories\Group\groupInterface', 'App\Repositories\Group\GroupRepository');
    }
}