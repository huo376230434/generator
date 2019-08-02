<?php
/**
 * Created by PhpStorm.
 * User: huo
 * Date: 18-8-1
 * Time: 下午2:09
 */
namespace Huojunhao\Generator\DwMake\D5MakeAdminControllerTraits;

use Illuminate\Support\Str;

trait  AddRoute{




    protected function addRoute()
    {

        //     todo   判断是否添加详情

        $route_content = file_get_contents($this->admin_route_path);
//        增加删除
        $this->route_data[] = <<<DDD
       \$router->delete("$this->route_uri/", "$this->route_controller_name@destroy");

DDD;

        $this->route_data[] = <<<DDD
    \$router->resource("$this->route_uri", $this->route_controller_name::class);
DDD;

//如果路由添加过，则把添加过的替换为空

        foreach ($this->route_data as $route) {
            $route = trim($route);
            if (Str::contains($route_content, trim($route))) {
                $route_content = str_replace($route, "", $route_content);
                file_put_contents($this->admin_route_path,$route_content);
            }
        }

        //添加功能测试
        $this->test_arr[] = [
            'type' => 'resource',
            'uri' => $this->route_uri
        ];

//        添加最终的hook
        $this->route_data[] = <<<DDD

    //admin_route_hook
DDD;


        $this->words_arr["//admin_route_hook"] = implode(" ", $this->route_data);

    }



}
