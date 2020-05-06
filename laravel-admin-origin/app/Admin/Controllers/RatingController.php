<?php

namespace App\Admin\Controllers;

use App\Models\Rating;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class RatingController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Rating';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Rating());

        $grid->column('id', __('Id'));
        $grid->column('user_id', __('User id'));
        $grid->column('teacher_id', __('Teacher id'));
        $grid->column('course_id', __('Course id'));
        $grid->column('score', __('Score'));
        $grid->column('is_show', __('Is show'));
        $grid->column('content', __('Content'));
        $grid->column('tags', __('Tags'));
        $grid->column('imgs', __('Imgs'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $show = new Show(Rating::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('teacher_id', __('Teacher id'));
        $show->field('course_id', __('Course id'));
        $show->field('score', __('Score'));
        $show->field('is_show', __('Is show'));
        $show->field('content', __('Content'));
        $show->field('tags', __('Tags'));
        $show->field('imgs', __('Imgs'));
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
        $form = new Form(new Rating());

        $form->number('user_id', __('User id'));
        $form->number('teacher_id', __('Teacher id'));
        $form->number('course_id', __('Course id'));
        $form->number('score', __('Score'));
        $form->number('is_show', __('Is show'));
        $form->text('content', __('Content'));
        $form->text('tags', __('Tags'));
        $form->text('imgs', __('Imgs'));

        return $form;
    }
}
