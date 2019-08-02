<?php
/**
 * Created by PhpStorm.
 * User: huo
 * Date: 18-8-1
 * Time: 下午2:07
 */
namespace Huojunhao\Generator\DwMake\D5MakeAdminControllerTraits;

use Illuminate\Support\Str;

trait AddGridContent{

    protected $grid_content = "";

    protected function addFields()
    {
        //        如果有字段，则显示字段
        foreach ($this->fields as $k => $v) {

            //如果有点，说明是有关联关系的
            if(Str::contains($k,".")) {
                [$relation, $key] = explode(".", $k);

                $this->grid_content .= <<<DDD
            \$grid->$relation()->$key("$v");

DDD;
                continue;
            }

//            如果名称是status,*_status,则加上状态的中文显示
            if(str_is("status",$k) || str_is('*_status',$k)){
                $this->grid_content .= <<<DDD
            \$grid->column("$k","$v")->display(function(\$status){
                return \$this->statusOptions(\$status);
            });

DDD;
                continue;

            }


            $this->grid_content .= <<<DDD
           \$grid->column("$k","$v");

DDD;




        }

    }

    protected function addAddButton()
    {
        //        是否要添加
        if($this->disable_add === 1){
            $this->grid_content .= <<<DDD

          \$grid->disableCreation();

DDD;
        }


    }


//    protected function addExcelBtn()
//    {
//        //        是否要数据excel导出
//        if($this->disable_excel === 0){
//
//
//            $this->grid_tools[] = " \$tools->append(new ExcelBtn(\$grid));\r\n";
//            $this->grid_content .= <<<DDD
//            \$grid->exporter(new CommonExporter(\$grid,"table",
//            //todo 补充excel 数据
//                [
////                    'id' => "卡号",
////                    'card_resource.price' => "金额",
////                    "card_resource.is_sale" => "是否卖出",
////                    'created_at' => "创建时间",
//                ],
//                [
////                'created_at'
//                ],
//                [ "price" ],[
////                    'is_sale' => function(\$active){
////                        return \$active==1 ? "已售出" : "未售";
////                    }
//                ]
//            ));
//DDD;
//
//        }
//    }


    protected function addRowSelector()
    {

//是否要多选功能
        if($this->disable_row_selecter === 1){
            $this->grid_content .= <<<DDD
          \$grid->disableRowSelector();

DDD;
        }

    }

    protected function addEditAction()
    {

//是否要编辑
        if($this->disable_edit===1 ){

            $this->grid_action[] = "\$actions->disableEdit();";

        }
    }

    protected function addShowAction()
    {

//是否要显示详情
        if($this->disable_view===1 ){

            $this->grid_action[] = "\$actions->disableView();";

        }
    }

    protected function addDeleteAction()
    {
        //是否要删除
        if(  $this->disable_delete === 1){
            $this->grid_action[] = "\$actions->disableDelete();";
            $this->grid_tools['batch'][] = <<<DDD
          \$batch->disableDelete();
    
DDD;
        }
    }


    protected function addFilter()
    {

        //        添加filter 相关
        if($this->disable_filter){
//            禁用filter
            $this->grid_content .= <<<DDD
            
\$grid->disableFilter();

DDD;

        }else{
            $filter_content = <<<DDD
        \$filter->disableIdFilter();
DDD;

            foreach ($this->filter['is'] as $k => $v) {
                $filter_content .= <<<DDD
                
                \$filter->equal("$v", "{$this->fields[$v]}");

DDD;
            }

            foreach ($this->filter['like'] as $k => $v) {
                $filter_content .= <<<DDD
                
                \$filter->like("$v", "{$this->fields[$v]}");
   
DDD;
            }

            foreach ($this->filter['select'] as $k => $v) {

                [$field,$method] = $v;
                $chinese_name = $this->fields[$field];

                //如果field有点，说明是关联关系，则需要field 转化成 *_id 这种形式
                if( Str::contains($field,".") ){
                    $field = explode('.', $field)[0];
                    $field = Str::snake($field)."_id";
                }
                $filter_content .= <<<DDD
                
                \$filter->equal("$field", "$chinese_name")->select($method);
  
DDD;
            }

            foreach ($this->filter['switch'] as $k => $v) {

                [$field,$method] = $v;
                $chinese_name = $this->fields[$field];

                $filter_content .= <<<DDD
                
                \$filter->equal("$field", "$chinese_name")->select($method);
   
DDD;
            }




            $filter_content .= <<<DDD
            
                \$filter->between('updated_at', '最近更新')->datetime();

DDD;

            $this->words_arr[ '//grid_handle_filter_hook'] = $filter_content;
//            $this->grid_content .= <<<DDD
//       \$grid->filter(function(Grid\Filter \$filter){
//
//                $filter_content
//            });
//DDD;
        }

    }




    protected function addGridAction()
    {


        //确认有没有自定义的action



        //添加grid_action 操作每条数据的操作
        if (!empty($this->grid_action)) {
            $grid_action_str = join("\r\n", $this->grid_action);

            $this->words_arr[ '//grid_handle_action_hook'] = $grid_action_str;

        }

    }




    protected function addGridTools()
    {

//        添加工具项,table上部的操作，包括批量操作
        if (!empty($this->grid_tools)) {


            //先将tools中的batch项转成str
            if (isset($this->grid_tools['batch']) && is_array($this->grid_tools['batch'])) {


                $batch_str = join("\r\n", $this->grid_tools['batch']);

                $this->words_arr[ '//grid_handle_batch_hook'] = $batch_str;
                unset($this->grid_tools['batch']);
            }

            $grid_tools_str = join("\r\n", $this->grid_tools);

            $this->words_arr[ '//grid_handle_tool_hook'] = $grid_tools_str;

        }


    }




    private function addGridContent()
    {

//grid_button_hook

        $this->addFields();
        $this->addAddButton();
//        $this->addExcelBtn();
        $this->addRowSelector();
        $this->addShowAction();
        $this->addEditAction();
        $this->addDeleteAction();
        $this->addFilter();
        $this->addGridAction();
        $this->addGridTools();
        $this->addShowFunction();


        $this->words_arr[ '//grid_handle_item_hook'] = $this->grid_content ;
    }

    protected function addShowFunction()
    {
        $str = "";
        foreach ($this->fields as $key => $field) {


//如果有点，说明是关联关系
            if(Str::contains($key,".")){
                [$relation, $column] = explode(".", $key);
                $str .= <<<DDD

           \$show->$relation$column("$field")->as(function(\$param){
               return \$this->$relation->$column;
           });

DDD;
                continue;

            }

            $str .= "\$show->" . $key . "('" . $field . "');".PHP_EOL;
        }
        $this->words_arr[ '//show_hook'] = $str ;


    }





}
