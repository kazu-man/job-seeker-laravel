<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Scout\ElasticsearchEngine;
use Elasticsearch\ClientBuilder;
use Laravel\Scout\EngineManager;

class ElasticsearchServiceProvider extends ServiceProvider
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
        resolve(EngineManager::class)->extend('elasticsearch', function ($app) {
            return new ElasticsearchEngine(
                config('scout.elasticsearch.index'),
                ClientBuilder::create()
                             ->setHosts(config('scout.elasticsearch.hosts'))
                             ->build()
                );
        });
    }
}