<?php

namespace App\Repositories\File;


use Illuminate\Support\ServiceProvider;


class FileRepoServiceProvide extends ServiceProvider
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
        $this->app->bind('App\Repositories\File\fileInterface', 'App\Repositories\File\FileRepository');
    }
}