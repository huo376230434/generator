<?php

namespace Huojunhao\DummyPackage;

use Illuminate\Support\ServiceProvider;

class DummyPackageServiceProvider extends ServiceProvider
{



    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        dump('DummyPackageServiceProvider');

//        $this->commands($this->commands);
    }

}
