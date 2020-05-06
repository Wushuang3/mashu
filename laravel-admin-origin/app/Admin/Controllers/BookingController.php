<?php

namespace App\Admin\Controllers;

use App\Models\Booking;
use App\Models\Member;
use App\Models\Course;
use App\Models\Teacher;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class BookingController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '预约记录';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Booking());
        $teachers= Teacher::GetKeyVall();
        $names= Member::findManagerByTeacher();
        $teacher_name = array();
        foreach($teachers as $k=>$v){
            $teacher_name[$k] = $names[$v];
        }

        $grid->column('id', __('Id'));
        $grid->column('name', __('姓名'));
        $grid->column('mobile', __('手机号'));
        $grid->column('time', __('时间'));
        //$grid->column('hour', __('时间段'));
        //$grid->column('teacher', __('教练'));
        $grid->column('teacher', __('教练'))->using($teacher_name);
        $grid->column('course', __('课程'))->using(Course::CourseMap());
        $grid->column('comment', __('备注'));
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('修改时间'));

        $grid->disableActions();
        $grid->disableCreateButton();

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
        $show = new Show(Booking::findOrFail($id));
        $teachers= Teacher::GetKeyVall();
        $names= Member::findManagerByTeacher();
        $teacher_name = array();
        foreach($teachers as $k=>$v){
            $teacher_name[$k] = $names[$v];
        }

        $show->field('id', __('Id'));
        $show->field('name', __('姓名'));
        $show->field('mobile', __('手机号'));
        $show->field('time', __('时间'));
        //$show->field('hour', __('时间段'));
        $show->field('teacher', __('教练'))->using($teacher_name);
        $show->field('course', __('课程'))->using(Course::CourseMap());
        $show->field('comment', __('备注'));
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
        $form = new Form(new Booking());

        /*
        $form->text('name', __('姓名'));
        $form->mobile('mobile', __('手机号'));
        $form->datetime('time', __('时间'))->default(date('Y-m-d'));
        $form->number('hour', __('时间段'));
        $form->number('teacher', __('教练'));
        $form->number('course', __('课程'));
        $form->textarea('comment', __('备注'));
        */

        return $form;
    }
}
