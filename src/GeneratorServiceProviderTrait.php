<?php

namespace Huojunhao\Generator;

use App\Lib\Common\CommonBase\FileUtil;
use Illuminate\Support\Str;

trait GeneratorServiceProviderTrait
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
        foreach ($this->getCommands() as $command) {
            $this->commands($command);

        }
    }


    protected function getCommands()
    {
        $this->generator_dir = $this->getGeneratorDir();
        $make_commands = FileUtil::allFile($this->generator_dir);
        $make_commands = $this->filtCommands($make_commands);

        return $make_commands;

    }

    protected function getGeneratorDir()
    {
        return  __DIR__.'/DwMake/';
    }

    protected function filtCommands($commands)
    {
//        dump($commands);
        $commands = collect($commands)
            ->filter(function($value,$key){
                return Str::endsWith( $value,".php");
            })
            ->map(function ($value,$key){
                return  substr($value,0,strrpos($value,'.php'));
            })
            ->filter(function ($value,$key){
                return class_exists($this->namespace_prefix . $value);
            })->map(function ($value,$key){
                return $this->namespace_prefix . $value;
            })->toArray();

        return $commands;
    }


}
