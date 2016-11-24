<?php 

Route::group(['middleware' => ['web']], function () {
	Route::get('/', function () {
    	return view('welcome');
	});
	Route::get('login',['uses'=>'bishopm\base\Http\Controllers\Auth\LoginController@showLoginForm','as'=>'showlogin']);
	Route::post('login',['uses'=>'bishopm\base\Http\Controllers\Auth\LoginController@login','as'=>'login']);

	Route::group(array(['middleware' => ['auth']]), function () {
		Route::get('/admin', function () {
		    return view('base::dashboard');
		});
		Route::post('logout',['uses'=>'bishopm\base\Http\Controllers\Auth\LoginController@logout','as'=>'logout']);
		Route::get('admin/households',['uses'=>'bishopm\base\Http\Controllers\HouseholdsController@index','as'=>'admin.households.index']);
		Route::get('admin/households/{household}',['uses'=>'bishopm\base\Http\Controllers\HouseholdsController@show','as'=>'admin.households.show']);
		Route::get('admin/households/{household}/edit',['uses'=>'bishopm\base\Http\Controllers\HouseholdsController@edit','as'=>'admin.households.edit']);
	});
});

















/*
|        | POST     | password/email                    |                        | App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail  | web,guest    |
|        | GET|HEAD | password/reset                    |                        | App\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm | web,guest    |
|        | POST     | password/reset                    |                        | App\Http\Controllers\Auth\ResetPasswordController@reset                | web,guest    |
|        | GET|HEAD | password/reset/{token}            |                        | App\Http\Controllers\Auth\ResetPasswordController@showResetForm        | web,guest    |
|        | GET|HEAD | register                          | register               | App\Http\Controllers\Auth\RegisterController@showRegistrationForm      | web,guest    |
|        | POST     | register                          |                        | App\Http\Controllers\Auth\RegisterController@register                  | web,guest    |
*/


	/*    Route::get('{society}/households/create',['uses'=>'HouseholdsController@create','as'=>'society.households.create']);
	    Route::post('{society}/households',['uses'=>'HouseholdsController@store','as'=>'society.households.store']);
	    Route::get('{society}/households/{household}/edit',['uses'=>'HouseholdsController@edit','as'=>'society.households.edit']);
	    Route::put('{society}/households/{household}',['uses'=>'HouseholdsController@update','as'=>'society.households.update']);
	    Route::delete('{society}/households/{household}',['uses'=>'HouseholdsController@destroy','as'=>'society.households.destroy']);*/

