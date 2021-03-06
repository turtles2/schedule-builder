<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Auth::check())
    {
    return view('home');
    }
    else
    {
    return view('auth/login');
    }
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/scheduling/availability', 'scheduling_controller@new_availability');
Route::post('/scheduling/availability', 'scheduling_controller@new_availability_store');
Route::post('/scheduling/availability/template', 'scheduling_controller@new_availability_template');

Route::group(['middleware' => 'can:manager'], function() {

    Route::get('/schedule_management/new', 'schedule_management_controller@new_schedule');
    Route::post('/schedule_management/new', 'schedule_management_controller@new_schedule_store');
});
