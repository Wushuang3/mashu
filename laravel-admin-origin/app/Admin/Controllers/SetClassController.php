<?php

namespace App\Admin\Controllers;

use App\Models\Course;
use App\Models\SetClass;
use App\Models\Teacher;
use App\Models\Member;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class SetClassController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '教练排班';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        $grid = new Grid(new SetClass());
        $teachers= Teacher::GetKeyVall();
        $names= Member::findManagerByTeacher();
        $teacher_name = array();
        foreach($teachers as $k=>$v){
            $teacher_name[$k] = $names[$v];
        }

        $grid->column('id', __('Id'));
        $grid->column('teacher', __('教练'))->using($teacher_name);
        $grid->column('time', __('时间'));
        $grid->column('hour', __('时间段'));
        $grid->column('course', __('课程'))->using(Course::CourseMap());
        $grid->column('num', __('预约人数'));
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('修改时间'));
        $grid->filter(function($filter){
            $filter->like('time', 'time');

        });

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
        $show = new Show(SetClass::findOrFail($id));
        $teachers= Teacher::GetKeyVall();
        $names= Member::findManagerByTeacher();
        $teacher_name = array();
        foreach($teachers as $k=>$v){
            $teacher_name[$k] = $names[$v];
        }
        $show->field('id', __('Id'));
        $show->field('teacher', __('教练'))->using($teacher_name);
        $show->field('time', __('时间'));
        $show->field('hour', __('时间段'));
        $show->field('course', __('课程'))->using(Course::CourseMap());
        $show->field('num', __('预约人数'));
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
        $teachers= Teacher::GetKeyVall();
        $names= Member::findManagerByTeacher();
        $teacher_name = array();
        foreach($teachers as $k=>$v){
            $teacher_name[$k] = $names[$v];
        }
        $form = new Form(new SetClass());
        $form->select('teacher', __('教练'))->options($teacher_name);
        $form->datetime('time', __('时间'))->default(date('Y-m-d'));
        $form->multipleSelect('hour', __('时间段'))->options(SetClass::HourMap());
        $form->select('course', __('课程'))->options(Course::CourseMap());
        $form->number('num', __('预约人数'));

        return $form;
    }

}
