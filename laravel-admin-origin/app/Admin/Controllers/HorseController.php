<?php

namespace App\Admin\Controllers;

use App\Models\Horse;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class HorseController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '马匹管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Horse());

        $grid->column('id', __('Id'));
        $grid->column('title', __('标题'));
        $grid->column('description', __('描述'));
        //$grid->column('content', __('Content'));
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('修改时间'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Horse::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('标题'));
        $show->field('images', __('图片'))->image();
        $show->field('description', __('描述'));
        $show->field('content', __('内容'));
        $show->field('created_at', __('创建时间'));
        $show->field('updated_at', __('修改时间'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Horse());

        $form->text('title', __('标题'));
        $form->image('images', __('图片'));
        $form->textarea('description', '描述');
        $form->textarea('content', '内容');
        return $form;
    }
}
