<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/' , 'ImpactController@startImpact');
Route::get('/impact_point' , 'ImpactController@impact');
Route::get('/fail/{?hours}' , 'ImpactController@fail');
Route::get('/tst/{ident}' , 'ImpactController@applicant');
Route::get('/ajax' , 'ImpactController@ajaxResponseGet');

