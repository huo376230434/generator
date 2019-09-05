<?php
namespace Huojunhao\Generator\DwMake\Utils;

use App\Lib\Common\CommonBase\FileUtil;
use Illuminate\Support\Str;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/13
 * Time: 18:44
 */
trait DwMakeTrait{
//    protected $template_words=[];
//    protected $replace_words=[];

    protected function make_stub($item,$template_words=null,$replace_words=null,$force=true){
        !$template_words && $template_words = $this->template_words;
        !$replace_words && $replace_words = $this->replace_words;

        $stub_path = $item['stub_path'];
        $des_path = $item['des_path'];
        $extra_params = $item['params'] ?? null;
//        dd(realpath($stub_path));
//        dd(is_dir($stub_path));
        if (!is_dir($stub_path)) {
            $this->moveFile($stub_path, $template_words, $replace_words, $des_path,$extra_params,$force);
        }
        else{
            //移动文件夹
            $files = FileUtil::allFile($stub_path);
            foreach ($files as $sub_file_path) {
                $temp_stub_path_dir = Str::finish($stub_path, '/');
                $temp_des_path_dir = Str::finish($des_path, '/');
                $temp_item = [
                    'stub_path' =>$temp_stub_path_dir.$sub_file_path,
                    'des_path' => $temp_des_path_dir.$sub_file_path
                ];
                if ($extra_params) {
                    $temp_item['params'] = $extra_params;
                }
                $this->make_stub($temp_item, $template_words, $replace_words,$force);
            }
        }
    }


    protected function moveFile($stub_path,$template_words,$replace_words,$des_path,$extra_params=null,$force=true)
    {
        if (!$force) {
            //如果不强制覆盖，则判断目标文件存不存在，存在则报已经存在
            if (is_file($des_path)) {
                $this->warn('已存在' . $des_path.' 不覆盖');
return false;
            }
        }
        $contents = file_get_contents($stub_path);
        $contents = str_replace($template_words,$replace_words,$contents);
        if ($extra_params){
            $contents = str_replace(array_keys($extra_params), array_values($extra_params),$contents);
        }
//        dump($stub_path);
        FileUtil::recursionFilePutContents($des_path, $contents);
        $this->info('生成' . $des_path);
    }

    protected function getBaseStubDir()
    {
        return __DIR__ . '/../../D5MakeStubs';

//        return storage_path('/stubs/D5MakeStubs');
    }


    protected function getBaseGeneratorSrcDir()
    {
        return __DIR__ . '/../src/';
    }

//
//    private function initSimpleDummy($words_arr,$refresh=false)
//    {
//
//        if ($refresh) {
//            $this->template_words = [];
//            $this->replace_words = [];
//        }
//        foreach ($words_arr as $k => $value) {
//            array_push($this->template_words, $k);
//            array_push($this->replace_words, $value);
//        }
//
//    }


//    protected function simpleMakeTask($tasks)
//    {
//
//        foreach($tasks as $key => $value){
//            $this->make_stub($value);
//        }
//    }


    protected function quickTask($words_arr,$tasks,$force=true)
    {

        $template_words = [];
        $replace_words=[];

        foreach ($words_arr as $k => $value) {
            array_push($template_words, $k);
            array_push($replace_words, $value);
        }

        foreach($tasks as $key => $value){
            $this->make_stub($value,$template_words,$replace_words,$force);
        }
    }

    protected function quickRemove($tasks=null)
    {
        if (is_null($tasks)) {
            $tasks = $this->getTasks();
        }
        foreach ( $tasks as $task) {
            $this->info('删除' . $task['des_path']);
            FileUtil::unlinkFileOrDir($task['des_path']);
        }
    }





    protected function initCommonFields($fields = [])
    {
        $new_arr = [];
        foreach ($fields as $key => $common_field) {
            $temp = explode("|", $key);
            foreach ($temp as $item) {
                $new_arr[$item] = $common_field;
            }
        }
       return $new_arr;
    }


    protected function composerDumpAutoload()
    {
        echo  shell_exec("composer dump");
    }


}
