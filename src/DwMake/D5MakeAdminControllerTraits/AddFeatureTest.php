<?php
/**
 * Created by PhpStorm.
 * User: huo
 * Date: 18-8-1
 * Time: 下午2:09
 */
namespace Huojunhao\Generator\DwMake\D5MakeAdminControllerTraits;

use App\Lib\Common\CommonBase\FileUtil;
use App\Lib\Common\Dictionary\BaseDict;

trait  AddFeatureTest{


    protected function addFeatureTest()
    {
        $this->makeTestDir();

//        先添加资源测试的父类
        $this->handleTestBase();


        foreach ($this->test_arr as $item) {
            switch ($item['type']){
                case 'get':
                    $this->handleGetTest($item);
                    break;
                case 'any' :
                case 'post':
                    $this->handlePostTest($item);
                    break;
                case 'resource':
                    $this->handleResourceTest($item);
            }

        }
//        添加功能测试

    }

    protected function handleTestBase()
    {

        if(is_file($this->feature_test_dir . 'FeatureTestBase.php')){
//            已经创建过
            return false;
        }

        $this->task_arr[] =   [
            'stub_path' =>$this->stub_dir."base_feature_test.stub.php",
            'des_path' => $this->feature_test_dir .  'FeatureTestBase.php'

        ];



    }

    protected function makeTestDir()
    {
        $this->feature_test_dir .=   "/" . $this->base_name.'/';
        //生成测试目录
        FileUtil::recursionMkDir($this->feature_test_dir);

    }

    protected function handleGetTest($item)
    {
        $test_name = ucfirst($item['uri'])."Test";

        $this->task_arr[] =   [
            'stub_path' =>$this->feature_test_stub_path,
            'des_path' => $this->feature_test_dir . $test_name . '.php',
            'params' => [
                'DummyTest' => $test_name,
                'DummyUri' => $this->getUri($item),
                'DummyMethod' => 'get'
            ]
        ];



    }

    protected function getUri($item)
    {
        return "admin/" . $this->route_uri . "/" . $item['uri'];

    }

    protected function handlePostTest($item)
    {
        $test_name = ucfirst($item['uri'])."Test";

        $this->task_arr[] =   [
            'stub_path' =>$this->feature_test_stub_path,
            'des_path' => $this->feature_test_dir . $test_name . '.php',
            'params' => [
                'DummyTest' => $test_name,
                'DummyUri' => $this->getUri($item),
                'DummyMethod' => 'post'
            ]
        ];
    }

    protected function handleResourceTest()
    {
        $route_list = BaseDict::resourceList();

        //如果是禁止操作的，则删除那个对应操作的url

        $route_list = collect($route_list)->whereNotIn("uri", $this->forbidden_actions);


        foreach ($route_list as $item) {
            $test_name = "Res".ucfirst($item['uri'])."Test";

            $temp_task =  [
                'stub_path' =>$this->feature_test_stub_path,
                'des_path' => $this->feature_test_dir . $test_name . '.php',
                'params' => [
                    'DummyTest' => $test_name,
                    'DummyUri' => $this->getResourceUri($item),
                    'DummyMethod' => $item['type']
                ]
            ];

            $this->task_arr[] =  $temp_task;
        }


    }

    protected function getResourceUri($item)
    {
        $uri = "admin/" . $this->route_uri ;
        if(in_array($item['uri'],['show','update','destroy','edit'])){
            $uri .= "/\$id";
            if ($item['uri'] == 'edit') {
                $uri .= "/edit";
            }
        }
        if ($item['uri'] == 'create') {
            $uri .= "/create";

        }
        return $uri;
    }


}
