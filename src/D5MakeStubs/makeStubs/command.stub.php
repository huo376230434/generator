<?php

namespace Huojunhao\Generator\DwMake;

use Huojunhao\Generator\DwMake\Utils\DwMakeBase;
use Illuminate\Support\Str;

class DwMakeDummyUpperCommand extends DwMakeBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dm:DummyCommand  {command_param} {--remove} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成';

    protected $stub_dir;

    protected $des_dir;
    protected $command_param;



    protected function init_configs()
    {
        $this->stub_dir = $this->getBaseStubDir().'/DummyCommandStubs/';
        $this->des_dir = app_path();
        $this->command_param = $this->argument("command_param");
        if (!$this->command_param) {
            $this->error('command_param 必填');
            die;

        }
    }

    protected function makeCommand()
    {
        $dummies = [
            "DummyCommand" => Str::snake($this->command_param),
            "DummyUpperCommand" => $this->command_param
        ];
        $this->quickTask($dummies, $this->getTasks());
    }



    protected function getTasks()
    {
        $tasks = [
            [
                'stub_path' => $this->stub_dir . 'DummyCommand.stub.php',
                'des_path' => $this->des_dir  . $this->command_param . ".php"
            ]
        ];
        return $tasks;

    }



}
