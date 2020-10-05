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
    return view('welcome');
});

Route::get('/crawler', 'CrawlerController@index');
Route::post('/crawler', 'CrawlerController@index');
Route::get('/confianca', 'CrawlerController@confianca');
Route::post('/confianca', 'CrawlerController@confianca');

Route::get('/phantom', 'CrawlerController@phantom');

Route::get('/camara/{id}/{quantidade}', 'CrawlerController@camaraBauru');

