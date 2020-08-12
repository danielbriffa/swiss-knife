<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/migration', function () {
    return view('migration');
});
Route::post('/migration-execute', 'MigrationController@create');

Route::get('/', function () {
    return view('welcome');
});
