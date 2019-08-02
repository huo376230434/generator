<?php

namespace App\Models;


use App\Models\Base\BaseModel;




class DummyModel extends BaseModel
{
    use ZZDummyModelRelationTrait,ZZDummyModelStaticTrait;


    public static function boot()
    {
        parent::boot();
//        static::deleting(function(DummyModel $model){
//            $model->indexTypeList()->delete();
//        });
    }




    //
}
