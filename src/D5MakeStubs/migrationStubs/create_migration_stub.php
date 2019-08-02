<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DummyClassName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('DummyTable', function (Blueprint $table) {
            $table->increments('id')->comment("主键ID");
            //extra_fields_hook


            $table->timestamp("created_at")->useCurrent()->comment("创建时间");
            $table->timestamp("updated_at")->useCurrent()->comment("更新时间");
        });
        DB::statement("ALTER TABLE DummyTable comment '注释' ");//表注释

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('DummyTable');
    }
}
