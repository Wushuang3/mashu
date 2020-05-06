<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->get('/word', 'HomeController@word');
    $router->resource('teachers', TeacherController::class);
    $router->resource('courses', CourseController::class);
    $router->resource('members', MemberController::class);
    $router->resource('bookings', BookingController::class);
    $router->resource('set-classes', SetClassController::class);



    $router->resource('tests', TestController::class);



});
