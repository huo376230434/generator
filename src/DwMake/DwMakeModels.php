<?php

namespace Huojunhao\Generator\DwMake;

use App\Lib\Common\CommonBase\FileUtil;
use App\Lib\Common\CommonBase\ScatteredUtil;
use App\Lib\Common\Dictionary\Dwfaker;
use App\Lib\Common\Utils\Env;
use Huojunhao\Generator\DwMake\Utils\DwMakeBase;
use Illuminate\Support\Str;

class DwMakeModels extends DwMakeBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dm:models  {--remove} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成批量模型的方法';

    protected $stub_dir;

    protected $factories_dir;

    protected $env_name = "database.env";

    protected $env_arr = [];
    protected $model_dir;
    protected $model_traits_dir;

    protected $base_namespace = "App\Model\\";

    protected $excluded_fields = ["id","created_at","updated_at"];
    protected $model_arr=[];

    protected $dwfaker_methods = [];

    protected $factory_arr = [];
    protected $shim_arr = [];

    protected $faker_common_fields = [
        "name|title" => <<<DDD

    str_random(rand(10,30))
,

DDD
        ,
        "email" => <<<DDD

    \$faker->unique()->safeEmail
,

DDD
        ,

        "content|*_content|description|note|*_note|comment|*_comment" => <<<DDD

    \$dwfaker->note()
,
DDD
        ,
        "*_id" => <<<DDD

    \$faker->numberBetween(0,6)
,

DDD
        ,
        "*_count|num|*_num|price|*_price" =><<<DDD

    \$faker->numberBetween(1,10000)
,

DDD
        ,
        "is_*|status|rate|can_*|type|*_type|*_state|state" => <<<DDD

    \$faker->numberBetween(0,1)
,

DDD
        ,
        "*_at" => <<<DDD

    \$faker->dateTimeBetween("-1 month")
,

DDD
        ,
        "url|*_url|pic|*_pic|avatar|img|*_img|img_url|*_img_url" => <<<DDD
    \$dwfaker->imgurl()
,
DDD
        ,
        "password|*_password" => <<<DDD

    bcrypt('123456ab')
,
DDD



    ];

    protected $faker_default_common_field = <<<DDD
    
    rand(10,1000)
,

DDD;




    protected function init_configs()
    {
        $this->stub_dir = $this->getBaseStubDir().'/modelsStubs/';
        $this->model_dir = app_path("Models/");

        $this->factories_dir = database_path("factories/");

        $this->env_arr = Env::resolve(storage_path("quickdev"),$this->env_name);

        $this->model_arr = $this->getModelArr();

        $this->factory_arr = FileUtil::allFileWithoutDir($this->factories_dir);
        $this->faker_common_fields =  $this->initCommonFields($this->faker_common_fields);
        $shim_path = $this->factories_dir . "aashim.php";
        if (is_file($shim_path)) {

            $this->shim_arr = require $shim_path;
            $this->shim_arr = array_dot($this->shim_arr);

        }
        // dd($this->shim_arr);
        $this->dwfaker_methods =  ScatteredUtil::getPublicMethods(Dwfaker::class) ? : [];

    }

    protected function makeCommand()
    {

        $this->init_configs();//初始化配置项
        //根据env 文件 进行创建表或者增加，删除字段等操作
        $this->doByEnv();


        //自动生成关联关系，目前只生成一对多
        $this->genModelRelations();

        //自动生成测试factories
        $this->genTestFactories();


//
//        $dummies = [
//            "models" => Str::snake($this->command_param),
//            "Models" => $this->command_param
//        ];
//        $this->quickTask($dummies, $this->getTasks());
    }



    protected function getTasks()
    {
        $tasks = [
            [
                'stub_path' => $this->stub_dir . 'models.stub.php',
                'des_path' => $this->des_dir  . $this->command_param . ".php"
            ]
        ];
        return $tasks;

    }



    protected function doByEnv()
    {

        foreach ($this->env_arr  as $key => $fields) {
            $table = str_plural(snake_case($key));
            //先判断模型存在不存在，不存在则生成


            if (!in_array($key, $this->model_arr)) {

                \Artisan::call("dm:model" ,[
                    "model" => $key
                ] );
            }

            if (!\Schema::hasTable($table)) {
                //表不存在，则创建表
                $this->createTable($key);
            }else{
                //表存在，则判断有没有添加或删除的字段
                $this->updateColumnsIfneeded($key);

            }
        }
//        dump($this->env_arr);

    }


    protected function updateColumnsIfneeded($key)
    {
        $table = str_plural(snake_case($key));
        $fields = explode(",",$this->env_arr[$key]);
        $add_arr = [];
        $drop_arr = [];
        foreach ($fields as $field) {
            if (!\Schema::hasColumn($table,$field)) {
                $add_arr[] = $field;
            }
        }


        !empty($add_arr) && \Artisan::call("dm:migration", [
            "table" => $table,
            "--type" => "add",
            "--fields" => implode(",", $add_arr)
        ]);

//判断是否有删除的字段

        foreach (\Schema::getColumnListing($table) as $item) {
            if(!in_array($item,$this->excluded_fields) && !in_array($item,$fields)){
                $drop_arr[] = $item;
            }
        };
//        dump($drop_arr);
        !empty($drop_arr) && \Artisan::call("dm:migration",[
            'table' => $table,
            "--type" => "drop",
            "--fields" => implode(",", $drop_arr)
        ]);

    }


    protected function createTable($key)
    {
        $fields = $this->env_arr[$key];
        \Artisan::call("dm:model", [
            "model" => $key,
            "--m" => true,
            "--fields" => $fields
        ]);
    }

    protected function getModelArr()
    {
        $models = FileUtil::allFileWithoutDir($this->model_dir);
        return collect($models)->map(function($value,$key){
            return substr($value,0,-4);
        })->toArray();

    }

    protected function genTestFactories()
    {
        collect($this->model_arr)->map(function($value,$key){
            $this->genTestFactory($value);
        });
        //生成全部测试数据的配置文件
        $this->genTotalConfig();

    }


    public function genTotalConfig()
    {

//        生成最终的全部生成factories;
        $factories_config = $this->factories_dir . "aatotal_config.php";
        if (file_exists($factories_config)) {
            $content = file_get_contents($factories_config);
            $content = trim($content);
            $content = rtrim($content, ';');
            $content = rtrim($content, "]");
        }else{
            $content = <<<DDD
<?php

return [

DDD;

        }
        $res = '';
        foreach ($this->getRealModelArr() as $item) {
//            先判断是否已经存在于配置过的文件里,存在的就不要再写进去了
            if (str_contains($content, "'".$item."'")) {
                continue;
            }
            $res .= <<<DDD
    [ 'item' => '$item', 'ignore' => false, 'num' => 6],

DDD;
        }
        $content .= $res;
        $content .= "];";
        file_put_contents($factories_config, $content);
        dump($res);

    }


    protected function getRealModelArr()
    {

        return  collect($this->model_arr)->filter(function($value,$key){
            //表不存在，则直接返回
            $table = str_plural(snake_case($value));
            if (!\Schema::hasTable($table)) {
                return false;
            }
            return true;
        })->toArray();
    }


    protected function factoryHasFixed($model)
    {
        return in_array('ZFix'.$this->getTestFactoryName($model), $this->factory_arr);



    }

    protected function genTestFactory($model)
    {
        //表不存在，则直接返回
        $table = str_plural(snake_case($model));
        if (!\Schema::hasTable($table)) {
            return false;
        }
        //已经生成过 ,则返回；20190307 不返回吧,避免字段更新不同步，每次都重新生成
        if ($this->factoryHasFixed($model)) {
            $this->info($this->getTestFactoryName($model)."已经被固定，不会再重新生成");
            //将原有的如果存在,则删除
            $des_path = $this->factories_dir . $this->getTestFactoryName($model);
            if (file_exists($des_path)) {
                $this->warn($this->getTestFactoryName($model)."已经存在的将删除");

                unlink($des_path);
            }
            return false;
        }
        $columns = \Schema::getColumnListing($table);
        $fake_columns_content = "";
        foreach ($columns as $column) {
            if ($column == "id") {
                //如果是ID，则让它自增即可
                continue;
            }

            //先判断是否有shim的配置，有的话优先走那个配置
            $res = $this->getShimFaker($model, $column);
            if($res !== false){
                $fake_columns_content .=  "'".$column ."' => ".$res.PHP_EOL;
//                dump($fake_columns_content);

            }else{
                $fake_columns_content .= "'".$column ."' => ".$this->getColumnFaker($column).PHP_EOL;
            }
        }

        $fake_columns_content = rtrim($fake_columns_content,",".PHP_EOL);

        $dummies = [
            "DummyModel" => $model,
            "//factoryhook" => $fake_columns_content
        ];
        $tasks = [
            [
                'stub_path' => $this->stub_dir . 'factory.stub.php',
                'des_path' => $this->factories_dir . $this->getTestFactoryName($model)
            ]
        ];

        $this->quickTask($dummies, $tasks);
    }


    protected function getShimFaker($model,$column)
    {
        if (empty($this->shim_arr)) {
            return false;
        }
        $arr_key = "App\Model\\".$model . '.' . $column;
        if (array_key_exists($arr_key, $this->shim_arr)) {
            $value = $this->shim_arr[$arr_key];
//先判断这个是不是dwfaker的方法名，如果是，则返回 dwfaker->methods这种
            if (in_array($value, $this->dwfaker_methods)) {
                $value = "\$dwfaker->$value()";
            }

            return str_finish($value,",");
        }
        return false;
    }

    protected function getColumnFaker($column)
    {
        //如果是 *_id ，则先判断有没有对应模型，有就直接返回模型ID
        if(str_is('*_id',$column)){

            $model= ucfirst(camel_case(str_replace("_id","",$column)));
            if(in_array($model,$this->model_arr)){
                return <<<DDD
                
    \App\Model\\$model::inRandomOrder()->value("id") ? :  \$faker->numberBetween(0,6)
,
DDD;

            }
        }

        foreach ($this->faker_common_fields as $key => $faker_common_field) {
            if (str_is($key, $column)) {
                return $faker_common_field;

            }
        }
        return $this->faker_default_common_field;
    }


    protected function getTestFactoryName($model)
    {
        return $model . "Factory.php";
    }





}
