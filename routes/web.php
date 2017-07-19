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


Route::get('/startPolling', 'BotController@start') ;
Route::get('/', 'TestController@start') ;
Route::get('/prof', 'ProfanityController@start');
Route::get('/gif', 'GifController@start');
Route::get('/getid', 'getIDController@start');
Route::get('/profgif', 'profgifController@start');