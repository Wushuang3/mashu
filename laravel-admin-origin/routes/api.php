<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/v1/booking/getTeacher', '\App\Http\Controllers\Api\V1\BookingController@getTeacher');
Route::get('/v1/booking/getTeacherClass', '\App\Http\Controllers\Api\V1\BookingController@getTeacherClass');
Route::post('/v1/booking/submitBooking', '\App\Http\Controllers\Api\V1\BookingController@submitBooking');
Route::get('/v1/booking/getBooking', '\App\Http\Controllers\Api\V1\BookingController@getBooking');
Route::get('/v1/booking/cancelBooking', '\App\Http\Controllers\Api\V1\BookingController@cancelBooking');
Route::get('/v1/booking/continueBooking', '\App\Http\Controllers\Api\V1\BookingController@continueBooking');
Route::post('/v1/booking/createRating', '\App\Http\Controllers\Api\V1\BookingController@createRating');
Route::get('/v1/booking/createAttention', '\App\Http\Controllers\Api\V1\BookingController@createAttention');
Route::get('/v1/booking/myAttention', '\App\Http\Controllers\Api\V1\BookingController@myAttention');
Route::get('/v1/booking/myRating', '\App\Http\Controllers\Api\V1\BookingController@myRating');
Route::get('/v1/booking/teacherDetail', '\App\Http\Controllers\Api\V1\BookingController@teacherDetail');
Route::get('/v1/booking/teacherRating', '\App\Http\Controllers\Api\V1\BookingController@teacherRating');
Route::get('/v1/booking/memberDetail', '\App\Http\Controllers\Api\V1\BookingController@memberDetail');
Route::post('/v1/booking/updateMember', '\App\Http\Controllers\Api\V1\BookingController@updateMember');
Route::post('/v1/booking/updateHeadIcon', '\App\Http\Controllers\Api\V1\BookingController@updateHeadIcon');
Route::post('/v1/booking/uploadImage', '\App\Http\Controllers\Api\V1\BookingController@uploadImage');
Route::get('/v1/booking/myCancelClass', '\App\Http\Controllers\Api\V1\BookingController@myCancelClass');
Route::get('/v1/booking/getTeacherBooking', '\App\Http\Controllers\Api\V1\BookingController@getTeacherBooking');
Route::get('/v1/booking/getUserIdByTeacher', '\App\Http\Controllers\Api\V1\BookingController@getUserIdByTeacher');
Route::get('/v1/booking/getMyCourse', '\App\Http\Controllers\Api\V1\BookingController@getMyCourse');
Route::get('/v1/booking/getCourseList', '\App\Http\Controllers\Api\V1\BookingController@getCourseList');
Route::get('/v1/booking/getCourseDetail', '\App\Http\Controllers\Api\V1\BookingController@getCourseDetail');
Route::get('/v1/booking/getSlides', '\App\Http\Controllers\Api\V1\BookingController@getSlides');
Route::get('/v1/booking/getHorses', '\App\Http\Controllers\Api\V1\BookingController@getHorses');
Route::get('/v1/booking/getPages', '\App\Http\Controllers\Api\V1\BookingController@getPages');
Route::get('/v1/booking/getHorseDetail', '\App\Http\Controllers\Api\V1\BookingController@getHorseDetail');
Route::get('/v1/booking/getMyOrder', '\App\Http\Controllers\Api\V1\BookingController@getMyOrder');
Route::post('/v1/booking/placeOrder', '\App\Http\Controllers\Api\V1\BookingController@placeOrder');
Route::post('/v1/booking/wxNotify', '\App\Http\Controllers\Api\V1\BookingController@wxNotify');

Route::post('/v1/booking/login', '\App\Http\Controllers\Api\V1\BookingController@login');
