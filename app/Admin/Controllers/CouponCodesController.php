<?php

namespace App\Admin\Controllers;

use App\Models\CouponCode;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;

class CouponCodesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'CouponCode';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CouponCode());

        // 默认按创建时间倒序排序
        $grid->model()->orderBy('created_at', 'desc');
        $grid->column('id', __('ID'))->sortable();
        $grid->column('name', __('名称'));
        $grid->column('code', __('优惠码'));
        $grid->column('description', __('描述'));
        $grid->column('usage', ('用量'))->display(function ($value) {
            return "{$this->used} / {$this->total}";
        });
        $grid->column('enabled', __('是否启用'))->display(function($value) {
            return $value ? '是':'否';
        });
        $grid->column('created_at', __('创建时间'));

        $grid->actions(function ($actions) {
            $actions->disableView();
        });

        return $grid;
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CouponCode());

        $form->display('id', __('ID'));
        $form->text('name', __('名称'))->rules('required');
        $form->text('code', __('优惠码'))->rules(function ($form) {
            // 如果 $form->model()->id 不为空，代表是编辑
            if ($id = $form->model()->id) {
                return 'nullable|unique:coupon_codes,code,'.$id.'id';
            } else {
                return 'nullable|unique:coupon_codes';
            }
        });
        $form->radio('type', __('类型'))->options(CouponCode::$typeMap)->rules('required')->default(CouponCode::TYPE_FIXED);
        $form->text('value', __('折扣'))->rules(function ($form) {
            if (request()->input('type') === CouponCode::TYPE_PERCENT) {
                // 如果选择了百分比折扣类型，那么折扣范围只能是 1 ~ 99
                return 'required|numeric|between:1,99';
            } else {
                // 否则只要大等于 0.01 即可
                return 'required|numeric|min:0.01';
            }
        });
        $form->text('total', __('总量'))->rules('required|numeric|min:0');
        $form->text('min_amount', __('最低金额'))->rules('required|numeric|min:0');
        $form->datetime('not_before', __('开始时间'));
        $form->datetime('not_after', __('结束时间'));
        $form->radio('enabled', __('启用'))->options(['1' => '是', '0' => '否']);

        // 事件处理器，在表单的数据被保存之前会触发
        // 如果没有输入优惠码，通过findAvailableCode生成
        $form->saving(function (Form $form) {
            if (!$form->code) {
                $form->code = CouponCode::findAvailableCode();
            }
        });

        return $form;
    }
}
