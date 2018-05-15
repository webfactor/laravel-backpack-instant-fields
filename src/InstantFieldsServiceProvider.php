<?php

namespace Webfactor\Laravel\Backpack\InstantFields;

use Illuminate\Support\ServiceProvider;
use Webfactor\Laravel\Generators\Commands\MakeBackpackCrudController;
use Webfactor\Laravel\Generators\Commands\MakeBackpackCrudModel;
use Webfactor\Laravel\Generators\Commands\MakeBackpackCrudRequest;
use Webfactor\Laravel\Generators\Commands\MakeEntity;

class InstantFieldsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(realpath(__DIR__ . '/../resources/views'), 'webfactor');

        // publish fields
        $this->publishes([__DIR__ . '/../resources/views/fields' => resource_path('views/vendor/backpack/crud/fields')], 'fields');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
