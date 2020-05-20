<?php

namespace App\Admin\Controllers;

use App\Models\Course;
use App\Models\Member;
use App\Models\Order;
use App\Models\Teacher;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use function foo\func;

class OrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '订单管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order());

        //$grid->column('id', __('Id'));
        $grid->column('order_id', __('订单号'));
        $grid->column('user_id', __('用户姓名'))->display(function($user_id){
           $user =  Member::where(['id'=>$user_id])->first();
           return $user->name;

        });
        $grid->column('course_id', __('套餐名称'))->display(function ($course_id){
            $course = Course::where(['id'=>$course_id])->first();
            if($course){
                return $course->title;
            }
            return '';

        });
        $grid->column('teacher_id', __('教练姓名'))->display(function ($teacher_id){
            $teacher = Teacher::where(['id'=>$teacher_id])->first();
            if($teacher){
                $user =  Member::where(['id'=>$teacher->user_id])->first();
                return $user->name;
            }
            return '';

        });
        $grid->column('price', __('金额'));
        $grid->column('status', __('状态'));
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
        $show = new Show(Order::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order_id', __('Order id'));
        $show->field('user_id', __('User id'));
        $show->field('course_id', __('Course id'));
        $show->field('price', __('Price'));
        $show->field('status', __('Status'));
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
        $form = new Form(new Order());

        $form->text('order_id', __('Order id'));
        $form->number('user_id', __('User id'));
        $form->number('course_id', __('Course id'));
        $form->number('price', __('Price'));
        $form->number('status', __('Status'));

        return $form;
    }
}
