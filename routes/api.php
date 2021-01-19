<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\Tutor\AuthController as TutorAuthController ;
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

Route::group([
    'prefix' => 'auth/user'
], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);    
});


Route::group([
    'prefix' => 'auth/tutor'
], function () {
    Route::post('/login', [TutorAuthController::class, 'login']);
    Route::post('/register', [TutorAuthController::class, 'register']);
    Route::post('/logout', [TutorAuthController::class, 'logout']);
    Route::post('/refresh', [TutorAuthController::class, 'refresh']);
    Route::get('/user-profile', [TutorAuthController::class, 'userProfile']);    
});