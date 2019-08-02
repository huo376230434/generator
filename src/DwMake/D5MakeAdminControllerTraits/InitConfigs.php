<?php
/**
 * Created by PhpStorm.
 * User: huo
 * Date: 18-8-1
 * Time: 下午2:09
 */
namespace Huojunhao\Generator\DwMake\D5MakeAdminControllerTraits;

use App\Lib\Common\CommonBase\FileUtil;
use Illuminate\Support\Str;

trait InitConfigs{


    protected function init_configs()
    {


//判断是否强制覆盖已经存在的文件
        $this->is_force = !!$this->argument('is_force');

//        模板文件根目录
        $this->stub_dir = $this->getBaseStubDir().'/admin_controllerStubs/';

        $this->config_path =  $config_path = storage_path("quickdev/admin_controller/admin_controller_config.php");

        $this->default_config_path = storage_path("quickdev/admin_controller/admin_controller_config_default.php");
        $des_config_dir = storage_path('quickdev/admin_controller/admin_controller_config');

        if( $config_name =  $this->option("config")){
            $config_path = $des_config_dir."/".ucfirst($config_name)."Controllerconfig.php";
            // dd($config_path);
            if(!is_file($config_path)){
                //throw new \Exception($config_name."配置文件不存在");
                $this->error("配置文件不存在");die;
            }
        }
        $configs = include $config_path;

//        设置默认参数
        $configs['route_path'] = $configs['route_path'] ?? "work";
        $configs["controller_dir"] = $configs["controller_dir"] ?? "Admin";
        $configs['model_dir'] = $configs['model_dir'] ?? "";

//        要生成的控制器的目录
        $this->controller_dir = app_path()."/Admin/Controllers/";
        $configs["controller_dir"] && $this->controller_dir .= $configs["controller_dir"] . "/";

//        要写入路由的文件路径
        $this->admin_route_path = app_path()."/Admin/admin_route/".$configs['route_path']."_route.php";

//      控制器的命名空间
        $this->base_namespace = "App\\Admin\\Controllers";
        $configs["controller_dir"] && $this->base_namespace .= "\\" . $configs['controller_dir'];

//        模型的命名空间
        $this->base_model_namespace = "App\\Models";
        $configs['model_dir'] && $this->base_model_namespace .= "\\" . $configs['model_dir'];

//        写死批量工具类的路径 及命名空间
        $this->batch_class_path = app_path() . "/Admin/Extensions/Widgets/Batch/";
        $this->batch_class_namespace = "App\\Extensions\\Widgets\\Batch";

//        路由的简称，如：/admin
        $this->route_uri = $configs['controller_name'];
        if ($configs['controller_name'] == "demo") {
            $this->error("请确认写好配置再执行");
            die;
        }
        $base_name = $this->base_name = ucfirst(Str::camel($configs['controller_name']));
//        路由中的控制器名称
        $this->route_controller_name = $this->controller_name =$base_name."Controller";

        $configs['controller_dir'] && $this->route_controller_name = $configs['controller_dir'] . "\\" . $this->route_controller_name;

//    模型名
        $this->model_name = $configs['model_name'] ? : $base_name;
        $this->title_header = $configs["title_header"];
        $this->add_menu = $configs['add_menu'];
        $this->menu_pid = $configs['menu_pid'];

        $this->disable_view = $configs["disable_view"] ?? 1;
        $this->disable_edit = $configs['disable_edit'];
        $this->disable_filter = $configs['disable_filter'] ?? 0;
        $this->disable_row_selecter = $configs['disable_row_selecter'];
//        $this->disable_excel = $configs['disable_excel'];

        $this->disable_add = $configs['disable_add'];
        $this->disable_delete = $configs['disable_delete'];

        $this->genFieldsAndFilters($configs['fields']);
        $this->row_show_field = $configs['row_show_field'];

        $this->extra_functions = $configs['extra_functions'];


        if (!$this->is_force) {
            //        判断控制器是否存在，若存在，则输出错误
            $this->check_is_exits();
        }

        $controller_trait_dir = $this->controller_dir . "/ControllerTrait/";
        !is_dir($controller_trait_dir) && FileUtil::recursionMkDir($controller_trait_dir);
        $this->controller_trait_path =$controller_trait_dir . $this->controller_name . "Trait.php";
        $controller_trait_stub_path = $this->stub_dir.'controller_trait.stub.php';
        //controllertrait移到文件夹下
        copy($controller_trait_stub_path, $this->controller_trait_path);
//配置文件备份到   resource/admin_controller_config 文件夹下

        FileUtil::recursionMkDir($des_config_dir);

        copy($config_path, $des_config_dir . '/'.$this->controller_name."config.php");


        $this->feature_test_stub_path = $this->stub_dir . "feature_test.stub.php";
        $this->feature_test_dir = base_path('tests/Feature/Admin');

        $this->browser_test_stub_path = $this->stub_dir . "browser_test.stub.php";
        $this->browser_test_dir = base_path('tests/Browser/Admin/');
        !is_dir($this->browser_test_dir) && mkdir($this->browser_test_dir);


        $this->controller_path = $this->controller_dir."/".$this->controller_name.".php";
        $controller_stub_path = $this->stub_dir . "controller.stub.php";
        //查看有没有controller,没有的话就复制一个
        if(!is_file($this->controller_path)){

            copy($controller_stub_path, $this->controller_path);
        }


        $this->controller_trait_extra_path = $controller_trait_dir . $this->controller_name . "ExtraTrait.php";
        $controller_trait_extra_stub_path = $this->stub_dir.'controller_extra_trait.stub.php';
        //查看有没有controllerextratrait,没有的话就复制一个
        if(!is_file($this->controller_trait_extra_path)){
            copy($controller_trait_extra_stub_path, $this->controller_trait_extra_path);
        }



    }


    protected function genFieldsAndFilters($fields)
    {

        $this->fields = [];

        foreach ($fields as $key => $field) {
            if (!Str::contains($field, "|")) {
                //不包含| 则不需要处理
                $this->fields[$key] = $field;
            }else{
                //分开
                $this->genRealFieldAndFilters($key, $field);
            }
        }

//        dump($this->fields);
//        dd($this->filter);
    }


    protected function genRealFieldAndFilters($key, $field)
    {

        [$name, $filter_type, $method] = array_pad(explode("|", $field),3,null);

        switch ($filter_type){
            case "is":
                $this->filter['is'][] = $key;
                break;
            case "like":
                $this->filter['like'][] = $key;
                break;
            case "select":
                $this->filter['select'][] = [$key,$this->getMethod($method,$key)];
                break;
            case "switch":
                $this->filter['switch'][] = [$key,$this->getMethod($method)];
                break;

            default:
                $this->error($filter_type . "这个搜索类型不存在");
                die;
        }


        $this->fields[$key] = $name;
    }


    protected function getMethod($method,$key=null)
    {

        if ($method === null && Str::contains($key,".")) {
            //此时是有关联关系的select, adminUser.name 这种，要生成默认的select
            [$model_name, $column] = explode(".", $key);
            $method = ucfirst($model_name)."@select";
        }

        if (Str::contains($method, "@")) {
            [$model, $method] = explode("@", $method);
        }else{
            $model = $this->model_name;
        }

        return "\App\Models\\".$model."::".$method."Options()";
    }

}
