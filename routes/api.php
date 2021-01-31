<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\RequestController as UserRequestController ;


use App\Http\Controllers\Master\AuthController as TutorAuthController ;
use App\Http\Controllers\Master\GroupController ;
use App\Http\Controllers\Master\RequestController as MasterRequestController ;
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
    Route::post('/activationcode', [AuthController::class, 'generateactivationcode']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);    
});

Route::group([
    'prefix' => 'user'
], function () {
    Route::post('/request', [UserRequestController::class, 'create']);

});


Route::group([
    'prefix' => 'auth/master'
], function () {
    Route::post('/login', [TutorAuthController::class, 'login']);
    Route::patch('/register', [TutorAuthController::class, 'register']);
    Route::post('/logout', [TutorAuthController::class, 'logout']);
    Route::post('/refresh', [TutorAuthController::class, 'refresh']);
    Route::post('/activationcode', [TutorAuthController::class, 'generateactivationcode']);
    Route::post('/verifyactivation', [TutorAuthController::class, 'verifyActivation']);
    Route::get('/user-profile', [TutorAuthController::class, 'userProfile']);    
});
Route::group([
    'prefix' => 'master'
], function () {
    Route::get('/groups', [GroupController::class, 'getAllGroups']);
    Route::post('/groups', [GroupController::class, 'createNewGroup']);
    Route::put('/groups', [GroupController::class, 'updateGroup']);

    Route::patch('/groups/{id}', [GroupController::class, 'updateStudentsStatus']);
    Route::delete('/groups/{id}', [GroupController::class, 'deleteGroup']);

    Route::get('/groups/{id}/students', [GroupController::class, 'getStudentsByGroupId']);
    
    Route::delete('/groups/{group_id}/students/{student_id}', [GroupController::class, 'deleteStudentInTheGroup']);
   

    Route::get('/request', [MasterRequestController::class, 'getAllRequests']);
    Route::post('/request/accept', [MasterRequestController::class, 'acceptRequest']);
    Route::delete('/request/decline', [MasterRequestController::class, 'deleteRequest']);

    Route::post('/request/validate/studentid', [MasterRequestController::class, 'validateStudentId']);


});
