<?php


namespace DummyControllerNamespace;
use App\Admin\Extensions\BaseExtends\AdminUtil;
use App\Admin\Extensions\AdminException;

use App\Admin\Controllers\AdminBase\AdminController;
use App\Admin\Extensions\BaseExtends\ModelForm;
use App\Admin\Extensions\Grid\Tools\BatchActions;
use App\Admin\Extensions\Show;
use App\Admin\Extensions\BaseExtends\Widgets\NormalLink;

use App\Admin\Extensions\BaseExtends\CsvExporter\CommonExporter;
use App\Admin\Extensions\CusAdmin;
use App\Http\Controllers\Controller;
use App\Admin\Extensions\BaseExtends\Widgets\ExcelBtn;
use Illuminate\Support\Facades\DB;
use App\Admin\Extensions\BaseExtends\Widgets\DoWithConfirm;
use App\Admin\Extensions\BaseExtends\Widgets\Batch\BatchDoWithConfirm;
use App\Admin\Extensions\BaseExtends\Widgets\Batch\BatchOperateWithMsg;
use App\Admin\Extensions\Form;
use App\Admin\Extensions\Grid;
use App\Admin\Extensions\Grid\Displayers\Actions;
use App\Admin\Extensions\Grid\Filter;
use App\Admin\Extensions\Grid\Tools;
use App\Admin\Extensions\Layout\Content;

//DummyNamespaces

class DummyControllerClass extends DummyParentController
{

    public $title_header = "dummy_title_header";
    protected $menu_uri="DummyControllerName";


}
