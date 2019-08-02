<?php
namespace Huojunhao\Generator\DwMake\Utils;

use App\Lib\Common\CommonBase\FileUtil;
use Illuminate\Console\Command;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/13
 * Time: 18:44
 */
class DwMakeBase extends Command{
    use DwMakeTrait;

    protected $template_words=[];
    protected $replace_words=[];

    protected function removePrefix()
    {

}

    protected function handleRemove()
    {
        $this->removePrefix();
        foreach ($this->getTasks() as $task) {
            FileUtil::unlinkFileOrDir($task['des_path']);
        }
        $this->removedCallback();
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


    protected function makeCommand()
    {
        // fixme 需要子类继承
    }



    protected function init_configs()
    {
        // fixme 需要子类继承
    }


    protected function getTasks()
    {
        // fixme 需要子类继承
    }



}
