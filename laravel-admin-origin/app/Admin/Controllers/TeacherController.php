<?php

namespace App\Admin\Controllers;

use App\Models\Member;
use App\Models\Teacher;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class TeacherController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '教练管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Teacher());
        $grid->column('id', __('Id'));
        $grid->column('user_id', __('姓名'))->using(Member::findManagerByTeacher());
        //$grid->column('name', __('姓名'));
        $grid->column('level', __('级别'))->using(Teacher::CourseMap());
        $grid->column('price', __('金额'));
        $grid->column('head_icon', __('头像'))->image("",50,50);
        $grid->column('description', __('描述'));
        $grid->column('is_index', __('推荐'))->using(Teacher::IsIndexMap());
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
        $show = new Show(Teacher::findOrFail($id));
        $show->field('id', __('Id'));
        //$show->field('user_id', __('姓名'));
        $show->field('user_id', __('姓名'))->using(Member::findManagerByTeacher());
        //$show->field('name', __('姓名'));
        $show->field('price', __('金额'));
        $show->field('level', __('级别'))->using(Teacher::CourseMap());
        $show->field('head_icon', __('头像'))->image();
        $show->field('description', __('描述'));
        $show->field('is_index', __('推荐'))->using(Teacher::IsIndexMap());
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
        $form = new Form(new Teacher());

        $form->select('user_id', __('姓名'))->options(Member::findManagerByTeacher());
       // $form->text('name', __('姓名'));
        $form->select('level', __('级别'))->options(Teacher::CourseMap());
        $form->number('price', __('金额'));
        $form->textarea('description', __('描述'));
        $form->image('head_icon', __('头像'));
        $form->select('is_index', __('推荐'))->options(Teacher::IsIndexMap());
        $form->datetime('created_at', __('创建时间'));
        $form->datetime('updated_at', __('修改时间'));
        //$form->hidden('user_id', __('user_id id'))->options(Member::findManagerByTeacher());

        return $form;
    }
}
