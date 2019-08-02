<?php

namespace Huojunhao\Generator;

use Illuminate\Support\ServiceProvider;

class GeneratorServiceProvider extends ServiceProvider
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

        dump('GeneratorServiceProvider');

//        $this->commands($this->commands);
    }

}
