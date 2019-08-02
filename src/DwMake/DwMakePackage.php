<?php

namespace Huojunhao\Generator\DwMake;

use App\Lib\Common\CommonBase\FileUtil;
use Huojunhao\Generator\DwMake\Utils\DwMakeBase;
use Huojunhao\Generator\DwMake\Utils\DwMakeTrait;
use Illuminate\Support\Str;

class DwMakePackage extends DwMakeBase
{
    use DwMakeTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dm:package  {command_param} {--remove}';

    /**
     * The console command description.
     *
     * @var string     */
    protected $description = '生成';


    protected $git_hub_dir;


    protected $stub_dir;
    protected $command_param;
    protected $des_dir;

    protected function removePrefix()
    {
        $provider_class = "huojunhao\\" . $this->command_param . '\\' . $this->command_param . 'ServiceProvider';
        if (class_exists($provider_class)) {
            $this->warn('必须先执行 composer remove huojunhao/' . Str::snake($this->command_param));
            die;
        }

    }

    protected function removedCallback()
    {

        $snake = Str::snake($this->command_param);
        $this->warn('删除成功，请手动在composer.json 中删除' .$snake  . '的配置; 而且由于git仓库的权限问题，需要手动把storage/git/'.$snake.'删除');
    }

    protected function initGit()
    {
        $command = "cd ".$this->git_hub_dir ." && git init && git add . && git commit -m 'init'";
        echo shell_exec($command);
    }

    public function tips()
    {
        $this->info('创建成功 ');
        $package_dir_name = Str::snake($this->command_param);
        $tips = <<<DDD
        {
            "type": "path",
            "url": "storage/git/$package_dir_name",
            "options": {
                "symlink": true
            }
        }
DDD;
        $this->warn(' 请在composer.json中 repositories中 添加以下代码:');
        $this->warn($tips);
        $this->warn('然后执行 composer require huojunhao/'.$package_dir_name.' --dev
  命令 再随便输入个命令，如 a dm:  测试是否正常');

    }


    protected function init_configs()
    {

//        $this->stub_dir = app_path(). '/Generator/CommandsStubs/D5MakeStubs/packageStubs/';

        $this->stub_dir = $this->getBaseStubDir().'/packageStubs/';
        $this->des_dir = app_path();

        $this->command_param = $this->argument("command_param");
//        dd($this->command_param);
        if (!$this->command_param) {
            throw new \Exception("command_param 必填");
        }
        $this->git_hub_dir = storage_path('git/'.Str::snake($this->command_param).'/');

    }

    protected function getProviderName()
    {
        return $this->command_param . 'ServiceProvider';
    }

    protected function makeCommand()
    {
        if (file_exists($this->git_hub_dir . 'composer.json')) {
            $this->error($this->command_param.'已存在');
            die;
        }

        $dummies = [
            "DummySnakePackage" => Str::snake($this->command_param),
            "DummyPackage" => $this->command_param
        ];
        $this->quickTask($dummies, $this->getTasks());
        $this->initGit();
        $this->tips();
    }

    protected function getTasks()
    {
        $tasks = [
            [
                'stub_path' => $this->stub_dir . 'dusk.composer.json',
                'des_path' => $this->git_hub_dir . 'composer.json'
            ],
            [
                'stub_path' => $this->stub_dir . 'provider.stub.php',
                'des_path' => $this->git_hub_dir . 'src/'.$this->getProviderName().'.php'
            ],
        ];
        return $tasks;

    }




}
