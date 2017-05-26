<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/welcome', ['middleware' => 'web', 'uses' => 'PageController@welcome'], function (){})->name('welcome');

Route::get('api/type/{type}/id/{id?}', ['middleware' => 'web', 'uses' => 'PageController@index'], function ($type, $id = null){})->name('all-folders');


Route::get('api/type/tasks/{id}', ['middleware' => 'web', 'uses' => 'PageController@load_single_task'], function ($id = null){})->name('single-task');

Route::get('api/type/tasks/{id}/modify/{amount}', ['middleware' => 'web', 'uses' => 'PageController@modify_allocated_time'], function ($id = null, $amount = null){})->name('single-task-modify');

Route::get('api/type/tasks/{id}/timelogs', ['middleware' => 'web', 'uses' => 'PageController@calculate_sum_logs'], function ($id = null){})->name('task-timelog');

Route::get('api/calculate-tasks/start/{start}/end/{end}', ['middleware' => 'web', 'uses' => 'PageController@load_all_tasks'], function ($start = "2017-01-01", $end = "2017-01-02"){})->name('calculate-tasks');

Route::get('api/calculate-tasks', ['middleware' => 'web', 'uses' => 'PageController@all_tasks_dates'], function (){})->name('calculate-tasks-dates');



Route::get('api/create-task/folder/{folder_id}/new-task/{task_id}/folder-code/{folder_code}', ['middleware' => 'web', 'uses' => 'PageController@create_task'], function ($folder_id = null, $task_id = null, $folder_code =null){})->name('create-task');


Route::get('api/report/start/{start}/end/{end}/initial', ['middleware' => 'web', 'uses' => 'PageController@generate_report_init'], function($start = "2017-01-01", $end = "2017-01-02") {})->name('report-generate-init');

Route::get('api/report/start/{start}/end/{end}/counter/{counter}/token/{token?}', ['middleware' => 'web', 'uses' => 'PageController@generate_report'], function($start = "2017-01-01", $end = "2017-01-02", $counter = 0, $nextPageToken = "") {})->name('report-generate');

Route::get('api/report', ['middleware' => 'web', 'uses' => 'PageController@report_dates'], function(){})->name('report-dates');


Route::get('{title}', ['middleware' => 'web', 'as' => 'other-page', 'uses' => 'PageController@index'] , function ($title) {});


// ADMIN PANEL ROUTES//
Route::group(['middleware' => ['web', 'admin'], 'prefix' => 'admin'], function()
{
	// route for dashboard
    Route::get('dashboard', 'Admin\AdminController@index');

    // [...] other routes

    // CRUD: Define the resources for the entities you want to CRUD.
    \CRUD::resource('task', 'Admin\TaskCrudController');
    \CRUD::resource('team', 'Admin\TeamCrudController');

});