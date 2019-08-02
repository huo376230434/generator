<?php
/**
 * Created by PhpStorm.
 * User: huo
 * Date: 18-8-1
 * Time: 下午2:39
 */
namespace Huojunhao\Generator\DwMake\D5MakeAdminControllerTraits;

trait FunctionTemplate{
    protected function functionTemplate($key,$v)
    {
        $arr = [
//            展示页面
            'show' => <<<DDD

   public function {$v['url']}(Content \$content)
    {
        AdminUtil::headerTitle(\$content,'$this->title_header 管理','');

            \$form = new Form(new \App\Models\\$this->model_name());

                AdminUtil::DefaultFormOptimize(\$form);
   
                \$form->builder()->setMode("edit");
                \$options = [0=> "选" ,1=> "择"];
                \$form->select("select","选择")->options(\$options);
                \$form->text('content', "内容");
            
//                \$form->datetime("expired_at", "过期时间");
                \$form->setAction("{$v['url']}Handle");
       

            \$content->body(\$form);
            return \$content;
      
    }
    

DDD
            ,
            //            处理页面

            'handle' => <<<DDD
            
   public function {$v['url']}Handle()
    {
        return   \$this->tryWithException(function(){




//        验证参数有效性
            \$this->validate(request(),[
                'content' => 'required',
                'select' => "required"
            ],[],[
                'content' => '内容',
                'select' => "选择"
            ]);
            
            
                    if (rand(1, 2) == 1) {
                throw new AdminException("测试随机失败");
            }

            
            DB::transaction(function() {
             // 处理逻辑
                 \$id = request("id");
                 \$content = request("content");
                 
                  // $this->model_name::where("id", \$id)->update(['state' => 1]);

                \$msg = "操作信息";
                 AdminUtil::log(\$msg);

            });


        },false,"操作成功");
        }
        

DDD
            ,
            'common' => <<<DDD

    public function {$v['url']}()
    {
        return   \$this->tryWithException(function(){

//        验证参数有效性
      //       \$this->validate(request(),[
      //           'content' => 'required',
      //       ],[],[
      //           'content' => '内容',
     //        ]);

            DB::transaction(function() {
             // 处理逻辑
         
            \$id = request("primary_key");
             \$content = request("content");
              // $this->model_name::where("id", \$id)->update(['state' => 1]);

                \$msg = "操作信息";
                AdminUtil::log(\$msg);
            });

        },true,"操作成功");
    }

DDD
            ,
            'batch_common' => <<<DDD

    public function {$v['url']}()
    {
        return   \$this->tryWithException(function(){

        \$ids = request('ids');
            if (!\$ids) {
                throw new AdminException("您没有选中任何选项！");

            }
            DB::transaction(function() use(\$ids){
             // 处理逻辑
               foreach(\$ids as \$id ){
              // $this->model_name::where("id", \$id)->update(['state' => 1]);

               }
                \$msg = "操作信息";
                OperateFlow::log(\$msg);
            });

        },true,"操作成功");
    }
    
    
DDD

        ];

        return $arr[$key];

    }

}

