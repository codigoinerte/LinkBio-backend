<?php


use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\GaleryModelController;
use App\Http\Controllers\Api\LinkController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ThemeController;
use App\Http\Controllers\Api\UserProfileDesignController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Upload;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
/*
!TODO: Corregir cuando no existe el bearer no debe redirigir a login sino devolver un error 401
*/
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
    Route::delete('delete-account', [AuthController::class, 'deleteAccount']);
    Route::post('validate-nickname', [AuthController::class, 'validateNickname']);
    Route::put('update-profile', [AuthController::class, 'updateFullBio']);
    Route::post('google', [AuthController::class, 'googleLogin']);
    Route::post('facebook', [AuthController::class, 'facebookLogin']);
});

Route::post('auth/validate-token', [AuthController::class, 'validateToken']);
Route::post('/landing', [LandingController::class, 'index']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('links', LinkController::class);
    Route::put('link/updateState/{id}', [LinkController::class, 'updateState']);
    Route::put('/updateOrderslinks', [LinkController::class, 'updateOrders']);
    Route::apiResource('themes', ThemeController::class);
    Route::apiResource('user-profile-design', UserProfileDesignController::class)->except(['update', 'store']);
    Route::post('user-profile-design', [UserProfileDesignController::class, 'upsertProfileDesign']);
    Route::post('upload/profile', [Upload::class, 'profile']);
    Route::delete('profile/photo', [Upload::class, 'deleteProfile']);
    Route::delete('profile/wallpaper', [UserProfileDesignController::class, 'deleteWallpaper']);
    Route::delete('galery/image/{id}', [GaleryModelController::class, 'deleteImageById']);
    Route::apiResource('projects', ProjectController::class)->except(['update']);
    Route::post('projects/update/{id}', [ProjectController::class, 'update']);
    Route::put('project/updateState/{id}', [ProjectController::class, 'updateState']);
    Route::put('/updateOrdersprojects', [ProjectController::class, 'updateOrders']);
});
