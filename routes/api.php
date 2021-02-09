<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\RequestController as UserRequestController ;


use App\Http\Controllers\Master\AuthController as TutorAuthController ;
use App\Http\Controllers\Master\GroupController ;
use App\Http\Controllers\Master\RequestController as MasterRequestController ;
use App\Http\Controllers\Master\HomeworkController as MasterHomeworkController ;
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
// Route::post('master/a', function (Request $request) {

//     error_log(print_r($request->all(),true));
//     return response()->json(200);
// });
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
    'prefix' => 'master/groups'
], function () {
    Route::get('/', [GroupController::class, 'getAllGroups']);
    Route::post('/', [GroupController::class, 'createNewGroup']);
    Route::put('/', [GroupController::class, 'updateGroup']);

    Route::patch('/{id}', [GroupController::class, 'updateStudentsStatus']);
    Route::delete('/{id}', [GroupController::class, 'deleteGroup']);

    Route::get('/{id}/students', [GroupController::class, 'getStudentsByGroupId']);
    
    Route::delete('/{group_id}/students/{student_id}', [GroupController::class, 'deleteStudentInTheGroup']);
   
});

Route::group([
    'prefix' => 'master/request'
], function () {
    Route::get('/', [MasterRequestController::class, 'getAllRequests']);
    Route::post('/accept', [MasterRequestController::class, 'acceptRequest']);
    Route::delete('/decline', [MasterRequestController::class, 'deleteRequest']);

    Route::post('/validate/studentid', [MasterRequestController::class, 'validateStudentId']);

});
Route::group([
    'prefix' => 'master/homeworks'
], function () {
    Route::get('/', [MasterHomeworkController::class, 'getAllHomeworks']);
    Route::post('/', [MasterHomeworkController::class, 'createHomeWork']);
    Route::post('/{id}', [MasterHomeworkController::class, 'updateHomeWork']);
    Route::delete('/{id}', [MasterHomeworkController::class, 'deleteHomeWork']);
    Route::put('/start', [MasterHomeworkController::class, 'startHomework']);
    Route::put('/end', [MasterHomeworkController::class, 'endHomework']);
});
