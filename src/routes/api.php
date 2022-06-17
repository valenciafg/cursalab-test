<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::apiResource('category', CategoryController::class);
Route::apiResource('subject', SubjectController::class);
Route::apiResource('course', CourseController::class);


Route::get('user', [UserController::class, 'index']);
Route::get('user/ranking/top/{top}', [UserController::class, 'userRanking']);
Route::get('user/{id}/course/{course_id}', [UserController::class, 'storeCourse']);
Route::get('user/{id}/course/{course_id}/subject/{subject_id}/score/{score}', [UserController::class, 'storeSubjectScore']);
