<?php

namespace Huojunhao\Generator\DwMake;

use App\Lib\Common\CommonBase\FileUtil;
use Huojunhao\Generator\DwMake\Utils\DwMakeBase;
use Illuminate\Support\Str;

class DwMakeMigration extends DwMakeBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dm:migration {table} {--type=create} {--fields=}  {--remove} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成迁移文件';
    protected $stub_dir;
    protected $migrate_dir;
    protected $table;
    protected $type;
    protected $fields;

    protected $common_fields = [
        "email" => <<<DDD
\$table->string('@key',100)->unique()->comment('');

DDD
        ,
        "content|description" => <<<DDD
\$table->text('@key')->nullable()->comment('');

DDD
        ,
        "*_id" => <<<DDD
\$table->unsignedInteger('@key')->default(0)->comment('');

DDD
        ,
        "*_count|num|*_num|price|*_price" =><<<DDD
\$table->integer('@key')->default(0)->comment('');

DDD
        ,
        "is_*|status|rate|can_*|type|*_type|*_status|*_state|state" => <<<DDD
\$table->unsignedTinyInteger("@key")->default(0)->comment("");

DDD
        ,
        "*_at" => <<<DDD
\$table->dateTime("@key")->nullable()->comment("");

DDD

    ];

    protected $default_common_field = <<<DDD
\$table->string('@key',150)->nullable()->comment('');

DDD;

    protected $default_drop_field = <<<DDD
\$table->dropColumn('@key');

DDD;


    protected function handleRemove()
    {
        $name_without_time = $this->getFileName(false) . ".php";
        $migration_files = FileUtil::allFile($this->migrate_dir);
        foreach ($migration_files as $migration_file) {
            if (Str::contains($migration_file, $name_without_time)) {
                FileUtil::unlinkFileOrDir($this->migrate_dir.'/'.$migration_file);
                $this->info('删除' . $migration_file);
            }
        }
        $this->removedCallback();
    }

    protected function makeCommand()
    {
        if ($this->has_created()) {
            $this->errorDie($this->getClassName() . "已经创建过,请先执行migrate 命令生成数据库表");
        }
        $replace_data = [
            "//extra_fields_hook" => $this->getExtraFields(),
            "DummyClassName" => $this->getClassName(),
            "DummyTable" => $this->table
        ];

        if($this->type != "create"){
            $replace_data["//extra_drop_fields_hook"] =$this->getExtraDropFields();
        }


        $this->quickTask($replace_data, $this->getTasks());
    }



    protected function getTasks()
    {
        $tasks = [
            [
                'stub_path' =>$this->stub_dir.$this->getMigrationStub(),
                'des_path' => $this->migrate_dir.$this->getFileName().".php"
            ]
        ];
        return $tasks;

    }





    protected function getExtraDropFields()
    {
        $arr = "";
        foreach ($this->getArrFields() as $field) {
            $arr .= str_replace("@key", $field, $this->default_drop_field);
        }
        return $arr;

    }


    protected function getMigrationStub()
    {
        switch ($this->type){
            case "create":
                return "create_migration_stub.php";
                break;
            case "add":
                return "add_column_migration_stub.php";
                break;
            case "drop":
                return "drop_column_migration_stub.php";
                break;

        }

    }


    protected function getExtraFields()
    {
        if ($this->fields){
            $field_arr = explode(",", $this->fields);

            $new_arr = '';
            foreach ($field_arr as $item) {
                $new_arr .= $this->getExtraField($item);
            }
            return $new_arr;
        }
        return "";
    }

    protected function getExtraField($item)
    {
        if ($key = $this->matchCommonField($item)) {
            return str_replace("@key", $item,$this->common_fields[$key]);
        }else{
            return str_replace("@key", $item,$this->default_common_field);
        }
    }

    protected function matchCommonField($item)
    {

        foreach ($this->common_fields as $key => $common_field) {

            if ( Str::is($key,$item)) {
                return $key;
            }
        }
        return false;

    }


     protected function init_configs()
    {

        $this->table =  $this->argument("table");

        $this->type = $this->option("type") ? : "create";

        $this->stub_dir = $this->getBaseStubDir().'/migrationStubs/';

        $this->migrate_dir = base_path("database/migrations/");

        $this->fields = $this->option("fields");
        $this->common_fields =  $this->initCommonFields($this->common_fields);
    }

    protected function getArrFields()
    {
        return explode(",", $this->fields) ? : [];

    }


    protected function getClassName()
    {
        $type = ucfirst($this->type);
        $table = ucfirst(Str::camel($this->table));
        $fields = ucfirst(Str::camel(implode("_",explode(",", $this->fields))));
        if ($this->type == "create") {
            return $type.$table."Table";
        }
        // dump($type);
        return $type.$fields."In".$table."Table";
    }

    private function getFileName($has_date=true)
    {
        if ($has_date) {
            return date("Y_m_d_His")."_".Str::snake($this->getClassName());
        }
        return Str::snake($this->getClassName());

    }

    protected function has_created()
    {
        $file_name = $this->getFileName(false);
        $arr = FileUtil::allFile($this->migrate_dir);
        foreach ($arr as $item) {
            if (Str::contains($item,$file_name)) {
                return true;
            }
        }
        return false;
    }




}
