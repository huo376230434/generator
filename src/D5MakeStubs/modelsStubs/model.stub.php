<?php

namespace App\Model;


use App\Model\Base\BaseModel;




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
