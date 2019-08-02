<?php

namespace Huojunhao\Generator\DwMake;

use Huojunhao\Generator\DwMake\Utils\DwMakeBase;
use Illuminate\Support\Str;

class DwMakeMake extends DwMakeBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dm:make {command_name} {--remove} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成生成器';

    protected $stub_dir;

    protected $command_dir;

    protected $command_name;

    protected $command_prefix="DwMake";


    protected function init_configs()
    {
        $this->stub_dir = $this->getBaseStubDir().'/makeStubs/';
        $this->command_dir = __DIR__.'/';
        $this->command_name = ucfirst($this->argument("command_name"));
        if (!$this->command_name) {
            $this->errorDie("command_name必填");
        }
    }
    protected function removedCallback()
    {
        parent::removedCallback();
        $this->warn('还需要手动把D5MakeStubs的'.Str::snake($this->command_name).'Stubs文件夹删除');
    }

    protected function getFileName()
    {
        return $this->command_dir . $this->command_prefix . $this->command_name . ".php";
    }

    public function makeCommand()
    {
        if (is_file($this->getFileName())) {
            $this->errorDie($this->command_name." 命令已经存在");
        }
        $dummies = [
            "DummyCommand" => Str::snake($this->command_name),
            "DummyUpperCommand" => $this->command_name
        ];
        $this->quickTask($dummies, $this->getTasks());
    }


    protected function getTasks()
    {
        $snake_name = Str::snake($this->command_name);
        $tasks = [
            [
                'stub_path' => $this->stub_dir . 'command.stub.php',
                'des_path' => $this->getFileName()
            ],
            [
                'stub_path' => $this->stub_dir . 'test.stub.php',
                'des_path' => $this->stub_dir ."/../".$snake_name  . "Stubs/".$snake_name.'.stub.php'
            ],
        ];
        return $tasks;

    }
}
