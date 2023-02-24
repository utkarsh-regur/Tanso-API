<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use  App\Http\Controllers\Api\UserController;
use  App\Http\Controllers\Api\ProjectController;

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

//LOGIN
Route::post('login', [UserController::class, 'login']);

//REGISTER
Route::post('register', [UserController::class, 'register']);

//PROFILE
Route::group(['middleware' => 'auth:api'], function(){
    Route::get('profile', [UserController::class, 'userDetails']);
   });


//ALL USERS
Route::group(['middleware' => 'auth:api'], function(){
    Route::get('users', [UserController::class, 'getAllUsers']);
   });

//ONE USER
Route::group(['middleware' => 'auth:api'], function(){
    Route::get('users/{id}', [UserController::class, 'getOneUser']);
   });

//CREATE USER   
Route::group(['middleware' => 'auth:api'], function(){
    Route::post('users', [UserController::class, 'createNewUser']);
   });      

//UPDATE USER   
Route::group(['middleware' => 'auth:api'], function(){
    Route::put('users/{id}', [UserController::class, 'updateUser']);
   }); 

//DELETE USER   
Route::group(['middleware' => 'auth:api'], function(){
    Route::delete('users/{id}', [UserController::class, 'deleteUser']);
   }); 

//PROJECT
Route::group(['middleware' => 'auth:api'], function(){
    Route::get('projects', [ProjectController::class, 'getAllProjects']);
   });

Route::group(['middleware' => 'auth:api'], function(){
    Route::get('projects/{id}', [ProjectController::class, 'getOneProject']);
   });

Route::group(['middleware' => 'auth:api'], function(){
    Route::post('projects', [ProjectController::class, 'createProject']);
   });

Route::group(['middleware' => 'auth:api'], function(){
    Route::put('projects/{id}', [ProjectController::class, 'updateProject']);
   });
   
Route::group(['middleware' => 'auth:api'], function(){
    Route::delete('projects/{id}', [ProjectController::class, 'deleteProject']);
   });