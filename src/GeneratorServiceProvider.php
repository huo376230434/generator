<?php

namespace Huojunhao\Generator;

use App\Lib\Common\CommonBase\FileUtil;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class GeneratorServiceProvider extends ServiceProvider
{
    use GeneratorServiceProviderTrait;
    protected $namespace_prefix = "Huojunhao\Generator\DwMake\\";

    protected $generator_dir = "";



}
