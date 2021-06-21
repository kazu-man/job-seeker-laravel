<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class JobSeekerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind(
            'JobListService', // キー名
            'App\Services\JobListService' // クラス名
        );
        $this->app->bind(
            'PostService', // キー名
            'App\Services\PostService' // クラス名
        );
        $this->app->bind(
            'InterviewService', // キー名
            'App\Services\InterviewService' // クラス名
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
