<?php

namespace App\Admin\Controllers;

use App\Models\Course;
use App\Models\Member;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class MemberController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '会员管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Member());

        $grid->column('id', __('Id'));
        $grid->column('name', __('姓名'));
        $grid->column('head_icon', __('头像'))->image("",50,50);
        $grid->column('mobile', __('手机号'));
        $grid->column('sex', __('性别'))->using(Member::SexMap());
        $grid->column('culture_num', __('文化课时'));
        $grid->column('experience_num', __('体验课时'));
        $grid->column('official_num', __('正式课时'));
        $grid->column('is_teacher', __('是否教练'))->using(Member::IsTeacherMap());
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
        $show = new Show(Member::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('姓名'));
        $show->field('head_icon', __('头像'))->image();
        $show->field('mobile', __('手机号'));
        $show->field('sex', __('性别'))->using(Member::SexMap());

        $show->field('culture_num', __('文化课时'));
        $show->field('experience_num', __('体验课时'));
        $show->field('official_num', __('正式课时'));
        $show->field('is_teacher', __('是否教练'))->using(Member::IsTeacherMap());
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
        $form = new Form(new Member());

        $form->text('name', __('姓名'));
        $form->image('head_icon', __('头像'));
        $form->mobile('mobile', __('手机号'));
        $form->select('sex', __('性别'))->options(Member::SexMap());
        $form->number('culture_num', __('文化课时'));
        $form->number('experience_num', __('体验课时'));
        $form->number('official_num', __('正式课时'));
        $form->select('is_teacher', __('是否教练'))->options(Member::IsTeacherMap());

        return $form;
    }
}
