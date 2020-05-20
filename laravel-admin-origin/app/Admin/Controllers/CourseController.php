<?php

namespace App\Admin\Controllers;

use App\Models\Course;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CourseController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '套餐管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Course());

        $grid->column('id', __('Id'));
        $grid->column('title', __('标题'));
        $grid->column('image', __('图片'))->image("",50,50);
       // $grid->column('culture', __('文化课'))->width(180);
        //$grid->column('experience', __(' 体验课'))->width(180);;
        //$grid->column('official', __('正式课'))->width(180);;
        //$grid->column('buy', __('购买须知'));
        //$grid->column('notice', __('注意须知'));
        $grid->column('price', __('金额'));
        $grid->column('valid_time', __('有效时间'));
        $grid->column('culture_num', __('文化课时'));
        $grid->column('experience_num', __('体验课时'));
        $grid->column('official_num', __('正式课时'));
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
        $show = new Show(Course::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('标题'));
        $show->field('image', __('图片'))->image();
        $show->field('culture', __('文化课'));
        $show->field('experience', __('体验课'));
        $show->field('official', __('正式课'));
        $show->field('buy', __('购买须知'));
        $show->field('notice', __('注意须知'));
        $show->field('price', __('金额'));
        $show->field('valid_time', __('有效时间'));
        $show->field('culture_num', __('文化课时'));
        $show->field('experience_num', __('体验课时'));
        $show->field('official_num', __('正式课时'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Course());

        $form->text('title', __('标题'));
        $form->image('image', __('图片'));
        $form->textarea('culture', __('文化课'));
        $form->textarea('experience', __('体验课'));
        $form->textarea('official', __('正式课'));
        $form->textarea('buy', __('购买须知'));
        $form->textarea('notice', __('注意须知'));
        $form->number('price', __('金额'));
        $form->text('valid_time', __('有效时间'));
        $form->number('culture_num', __('文化课时'));
        $form->number('experience_num', __('体验课时'));
        $form->number('official_num', __('正式课时'));

        return $form;
    }
}
