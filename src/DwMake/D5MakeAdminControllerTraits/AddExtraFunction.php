<?php
/**
 * Created by PhpStorm.
 * User: huo
 * Date: 18-8-1
 * Time: 下午2:09
 */
namespace Huojunhao\Generator\DwMake\D5MakeAdminControllerTraits;

use App\Admin\Extensions\BaseExtends\Widgets\NormalLink;

trait  AddExtraFunction{
    use FunctionTemplate;

    protected $batch_tool_default = [
        "BatchDoWithConfirm", "BatchOperateWithMsg"
    ];
    protected function addExtraFunction()
    {

        if (empty($this->extra_functions)) {
            return false;
        }

        $extra_functions = '';
        $this->words_arr['//controller_trait_hook'] = '';
        foreach ($this->extra_functions as $k => $v) {

            //验证方法是否已经添加过

//         如果是有表单页面的：
            switch ($v['type']) {
                case "page_form":
                    $extra_functions = $this->hasPageFunction($extra_functions, $v);
                    break;
                case "batch_tool":
                    $extra_functions = $this->batchToolFunction($extra_functions,$v);
                    break;
                case "action" :
                    $extra_functions = $this->extraActions($extra_functions, $v);
                    break;
                case "action_page_form":
                    $extra_functions = $this->actionPageFormFunction($extra_functions,$v);
                    break;
            }

            //添加函数样板代码到控制器中
            $this->words_arr['//controller_trait_hook'] = $extra_functions;

        }
        $this->words_arr['//controller_trait_hook'] .= <<<DDD
//controller_trait_hook
DDD;
        return $this->words_arr;
    }

    protected function actionPageFormFunction($extra_functions,$v)
    {
        $extra_functions = $this->hasPageFunction($extra_functions,$v,false);
        $v['button_type'] = 'NormalLink';
        //            添加action按钮
        $this->grid_action[] = <<<DDD
        
        \$actions->append(new {$v['button_type']}("{$v['name']}",url('admin/$this->route_uri/{$v['url']}'),  \$row->id));
DDD;
        return $extra_functions;
    }


    protected function methodIsAdded($v)
    {
        $controller_trait_content = file_get_contents($this->controller_trait_extra_path);
        $search_method_name = "function " . $v['url'] . "(";
        return str_contains( $controller_trait_content,$search_method_name);

    }

    protected function extraActions($extra_functions,$v)
    {
        if(!$this->methodIsAdded($v)) {

            //        添加批量处理函数
            $extra_functions .= $this->functionTemplate('common', $v);

            //添加路由
            $this->route_data[] = <<<DDD
       \$router->any("$this->route_uri/{$v['url']}", "$this->route_controller_name@{$v['url']}");

DDD;
            //添加功能测试
            $this->test_arr[] = [
                'type' => 'any',
                'uri' => $v['url']
            ];



        }



//            添加action按钮

        $this->grid_action[] = <<<DDD
        
        \$actions->append(new {$v['button_type']}("{$v['name']}",url('admin/$this->route_uri/{$v['url']}'),  \$row->id));
DDD;




        return $extra_functions;
    }


    protected function hasPageFunction($extra_functions,$v,$add_btn=true)
    {

        if(!$this->methodIsAdded($v)){
            //添加展示页面的函数
            $extra_functions .= $this->functionTemplate('show', $v);

//          添加处理函数
            $extra_functions .= $this->functionTemplate('handle', $v);

            //添加路由
            $this->route_data[] = <<<DDD
       \$router->get("$this->route_uri/{$v['url']}/","$this->route_controller_name@{$v['url']}") ;
       \$router->any("$this->route_uri/{$v['url']}Handle", "$this->route_controller_name@{$v['url']}Handle");

DDD;

            //添加功能测试
            //添加功能测试
            $this->test_arr[] = [
                'type' => 'get',
                'uri' => $v['url']
            ];
            $this->test_arr[] = [
                'type' => 'any',
                'uri' => $v['url']."Handle"
            ];


        } ;

        if ($add_btn) {
            //grid 中添加按钮
            $this->grid_tools[] = <<<DDD
              \$tools->append("<div class=\"btn-group pull-right\" style=\"margin-right: 10px\">
    <a href=\"/admin/$this->route_uri/{$v['url']}\" class=\"btn btn-sm btn-info\">
        <i class=\"fa fa-save\"></i>&nbsp;&nbsp;{$v['name']}
    </a>
</div>");
DDD;
        }

        return $extra_functions;

    }


    protected function batchToolFunction($extra_functions,$v)
    {


        if(!$this->methodIsAdded($v)) {
            //        添加批量处理函数
            $extra_functions .= $this->functionTemplate('batch_common', $v);

            //添加路由
            $this->route_data[] = <<<DDD
       \$router->any("$this->route_uri/{$v['url']}", "$this->route_controller_name@{$v['url']}");

DDD;
            //添加功能测试
            $this->test_arr[] = [
                'type' => 'any',
                'uri' => $v['url']
            ];


        }


        //                如果是批量操作的：
        !isset($this->grid_tools['batch']) && $this->grid_tools['batch'] = [];

        if (in_array($v['batch_type'], $this->batch_tool_default)) {
            //是默认的两个的话就不用再加类了

            //        添加grid中的batch操作按钮
            $this->grid_tools['batch'][] = <<<DDD
                
          \$batch->add("{$v['name']}", new {$v['batch_type']}(url('admin/$this->route_uri/{$v['url']}'),"{$v['name']}"));
    
DDD;
        }else{

            //将类的首字母大写
            $batchClassName = ucwords($v['url']);

//        添加grid中的batch操作按钮
            $this->grid_tools['batch'][] = <<<DDD
                
    \$batch->add("{$v['name']}",new $batchClassName(url('admin/$this->route_uri/{$v['url']}'),"{$v['name']}"));
DDD;

//        添加操作batch类
            $this->task_arr[] =
                ['stub_path' =>$this->stub_dir."batch_tool.stub.php", 'des_path' => $this->batch_class_path.$batchClassName.".php"];//替换路由

//        添加替换batch类名
            $this->words_arr['DummyBatchStub'] = $batchClassName;

//        添加到控制器中的namespace
            $this->use_namespaces[] = "use App\Admin\Extensions\Widgets\Batch\\" . $batchClassName.";";

        }


        return $extra_functions;

    }

}
