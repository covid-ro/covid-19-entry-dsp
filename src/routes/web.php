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

/* Authentication Routes */
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

/* App Routes */
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/', 'HomeController@index')->name('home');
Route::get('/declaratie/{code}', 'HomeController@show')->name('declaratie');
Route::post('/change-lang', 'HomeController@postChangeLanguage')->name('change-lang');
Route::post('refresh-list', 'HomeController@postRefreshList')->name('refresh-list');
Route::post('search-declaration', 'HomeController@postSearchDeclaration')->name('search-declaration');
Route::post('register-declaration', 'HomeController@postRegisterDeclaration')->name('register-declaration');
