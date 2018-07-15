<?php

namespace Webfactor\Laravel\Backpack\InstantFields;

use Illuminate\Support\ServiceProvider;

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
        $this->publishes([__DIR__ . '/../resources/views/fields' => resource_path('views/vendor/backpack/crud/fields')], 'instantfields');
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
