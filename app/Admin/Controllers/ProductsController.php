<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProductsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '商品';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product());
        //sortable 排序
        $grid->column('id', __('ID'))->sortable();
        $grid->column('title', __('商品名称'));
        $grid->column('on_sale', __('已上架'))->display(function ($value) {
            return $value ? '是':'否';
        });
        $grid->column('price', __('价格'));
        $grid->column('rating', __('评分'));
        $grid->column('sold_count', __('销量'));
        $grid->column('review_count', __('评论数'));
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
        });
        $grid->tools(function ($tools) {
            //禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
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
        $form = new Form(new Product());

        /*
        * rules('required')
        */
        $form->text('title', __('商品名称'))->rules('required');
        $form->image('image', __('封面图片'))->rules('required|image');
        $form->quill('description', __('商品描述'))->rules('required');
        // 创建一组单选框
        $form->radio('on_sale',__('上架'))->options(['1' => '是', '0' => '否'])->default('0');

        // 直接添加一对多的关联模型
        $form->hasMany('skus',  __('SKU 列表'), function(Form\NestedForm $form){
            $form->text('title', __('SKU 名称'))->rules('required');
            $form->text('description', __('SKU 描述'))->rules('required');
            $form->text('price', __('单价'))->rules('required|numeric|min:0.01');
            $form->text('stock', __('剩余库存'))->rules('required|integer|min:0');
        });

        /*
        * 定义事件回调，当模型即将保存时会触发这个回调
        * 再保存商品之前拿到所有SKU中最低的价格作为商品的价格，通过 $form->model()->price 存入到商品模型中
        * collect() 函数是laravel提供的一个辅助函数，可以快速创建一个Collection对象，通过collect里的min()方法找到SKU里最小的price
        */
        $form->saving(function (Form $form) {
            $form->model()->price = collect($form->input('skus'))
                ->where(Form::REMOVE_FLAG_NAME, 0)
                ->min('price') ?: 0;

        });

        return $form;
    }
}
