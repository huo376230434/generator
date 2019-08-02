<?php
namespace Huojunhao\Generator\DwMake\Utils;

use App\Lib\Common\CommonBase\FileUtil;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/13
 * Time: 18:44
 */
abstract  class DwMakeBase extends Command{
    use DwMakeTrait;

    protected $template_words=[];
    protected $replace_words=[];

    protected $model_dir;

    public function __construct()
    {
        parent::__construct();
        $this->model_dir = app_path("Models/");

    }

    protected function getModelArr()
    {
        $models = FileUtil::allFileWithoutDir($this->model_dir);
        return collect($models)->reject((function ($value,$key){
            return Str::startsWith($value,'ZZ');
        }))->map(function($value,$key){
            return substr($value,0,-4);
        })->toArray();
    }

    protected function removePrefix()
    {

}

    protected function handleRemove()
    {
        $this->removePrefix();
        foreach ($this->getTasks() as $task) {
            $this->info('删除' . $task['des_path']);
            FileUtil::unlinkFileOrDir($task['des_path']);
        }
        $this->removedCallback();
    }

    protected function errorDie($msg)
    {
        $this->error($msg);
        die;
    }

    protected function removedCallback()
    {
        $this->info('删除完毕!');
    }


    public function handle()
    {

        $this->init_configs();//初始化配置项
        if ($this->option('remove')) {
            $this->handleRemove();
            return ;
        }
        $this->makeCommand();
    }


    abstract protected function makeCommand();

    abstract protected function init_configs();

    abstract protected function getTasks();


}
