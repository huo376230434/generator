<?php
/**
 * Created by PhpStorm.
 * User: huo
 * Date: 18-8-1
 * Time: 下午2:07
 */
namespace Huojunhao\Generator\DwMake\D5MakeAdminControllerTraits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait AddFormContent{



    protected function addFormContent()
    {
        //form_handle_button_hook
        $str = '';
        foreach ($this->fields as $k => $v) {

            //判断是否有select ,在filter中,如果有，则用select
            if($select_column = Arr::first($this->filter['select'],function($value,$key)use ($k){
                return $value[0] == $k;
            })){
                $method = $select_column[1];
                //如果field有点，说明是关联关系，则需要field 转化成 *_id 这种形式
                if( Str::contains($k,".") ){
                    $k = explode('.', $k)[0];
                    $k = Str::snake($k)."_id";
                }
                $str .= <<<DDD

            \$form->select("$k", "$v")->options($method)->rules("required");

DDD;
            }else if($switch = Arr::first($this->filter['switch'],function($value,$key)use ($k){
                //判断是否有 switch ,在filter中,如果有，则用 switch
                return $value[0] == $k;
            })){
                $method = $switch[1];
                $str .= <<<DDD
            \$form->switch("$k", "$v")->options($method)->rules("required");
DDD;

            }else{
                $str .= <<<DDD
          
            \$form->text("$k", "$v")->rules("required");

DDD;
            }
        }

        $this->words_arr[ '//form_handle_button_hook'] = $str ;
        $this->words_arr['dummy_row_show_field'] = $this->row_show_field;
    }

}
