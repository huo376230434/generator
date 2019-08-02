<?php

use Faker\Generator as Faker;


$factory->define(App\Model\DummyModel::class, function (Faker $faker) {
    $dwfaker = new \App\Lib\Common\Dictionary\Dwfaker();
    return [
        //factoryhook
    ];
});
