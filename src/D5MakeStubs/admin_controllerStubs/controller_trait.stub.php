<?php
/**
 * Created by PhpStorm.
 * User: huo
 * Date: 18-8-6
 * Time: 上午10:16
 */
namespace DummyControllerNamespace\ControllerTrait;



use App\Admin\Extensions\BaseExtends\Widgets\Bt3Modals\OperateWithMsg;
use App\Admin\Extensions\AdminException;
use App\Admin\Extensions\Form;
use App\Admin\Extensions\Grid;
use App\Lib\Common\CommonBase\FileUtil;
use App\Lib\Common\CommonBase\UrlUtil;
use App\Admin\Extensions\BaseExtends\Widgets\NormalLink;

use Encore\Admin\Grid\Tools\BatchActions;
use Illuminate\Support\Facades\DB;
use App\Admin\Extensions\BaseExtends\Widgets\DoWithConfirm;
use App\Admin\Extensions\BaseExtends\Widgets\Batch\BatchDoWithConfirm;
use App\Admin\Extensions\BaseExtends\Widgets\Batch\BatchOperateWithMsg;
use App\Admin\Extensions\BaseExtends\AdminUtil;

//DummyNamespaces

trait DummyControllerClassTrait {


    public function defaultGrid(Grid $grid, $_this)
    {
        //grid_handle_item_hook

    }


    public function defaultGridActions(Grid\Displayers\Actions $actions, $_this)
    {
        $row = $actions->row;

        //grid_handle_action_hook
    }


    public function defaultGridTools(Grid\Tools $tools ,$_this)
    {
        //grid_handle_tool_hook
    }


    public function defaultGridBatchs(BatchActions $batch,$_this )
    {
        //grid_handle_batch_hook

    }


    public function defaultGridFilters(Grid\Filter $filter ,$_this)
    {
        //grid_handle_filter_hook
    }


    public function defaultForm(Form $form ,$_this)
    {

        //form_handle_button_hook

    }

}
