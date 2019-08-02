<?php
namespace Huojunhao\Generator\DwMake\Utils;

use App\Lib\Common\CommonBase\FileUtil;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/13
 * Time: 18:44
 */
trait DwMakeTrait{
//    protected $template_words=[];
//    protected $replace_words=[];

    protected function make_stub($item,$template_words=null,$replace_words=null){
        !$template_words && $template_words = $this->template_words;
        !$replace_words && $replace_words = $this->replace_words;

        $stub_path = $item['stub_path'];
        $des_path = $item['des_path'];
        $contents = file_get_contents($stub_path);
        $contents = str_replace($template_words,$replace_words,$contents);
       if (isset($item['params'])){
           $contents = str_replace(array_keys($item['params']), array_values($item['params']),$contents);
       }
        FileUtil::recursionFilePutContents($des_path, $contents);
//        file_put_contents($des_path,$contents);
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


    protected function quickTask($words_arr,$tasks)
    {

        $template_words = [];
        $replace_words=[];

        foreach ($words_arr as $k => $value) {
            array_push($template_words, $k);
            array_push($replace_words, $value);
        }

        foreach($tasks as $key => $value){
            $this->make_stub($value,$template_words,$replace_words);
        }
    }




    protected function initCommonFields($fields = [])
    {
        $new_arr = [];
        foreach ($fields as $key => $common_field) {
            $temp = explode("|", $key);
//            dump($temp);
            foreach ($temp as $item) {
                $new_arr[$item] = $common_field;
            }
        }
       return $new_arr;
    }


}
