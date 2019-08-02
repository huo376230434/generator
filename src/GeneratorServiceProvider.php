<?php

namespace Huojunhao\Generator;

use App\Lib\Common\CommonBase\FileUtil;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class GeneratorServiceProvider extends ServiceProvider
{

    protected $namespace_prefix = "Huojunhao\Generator\DwMake\\";

    protected $generator_dir = "";


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

        foreach ($this->getCommands() as $command) {
            $this->commands($command);

        }
//        $this->commands($this->commands);
    }


    protected function getCommands()
    {
        $this->generator_dir = __DIR__.'/DwMake/';
        $make_commands = FileUtil::allFile($this->generator_dir);
        $make_commands = $this->filtCommands($make_commands);

        return $make_commands;

    }

    protected function filtCommands($commands)
    {
        $commands = collect($commands)
            ->filter(function($value,$key){
                return Str::endsWith( $value,".php");
            })
            ->map(function ($value,$key){
                return  trim( $value,".php");
            })
            ->filter(function ($value,$key){
                return class_exists($this->namespace_prefix . $value);
            })->map(function ($value,$key){
                return $this->namespace_prefix . $value;
            })->toArray();

        return $commands;
    }


}
