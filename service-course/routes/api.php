<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\LessonsController;
use App\Http\Controllers\MentorsController;
use App\Http\Controllers\ReviewsController;
use App\Http\Controllers\ChaptersController;
use App\Http\Controllers\MyCoursesController;
use App\Http\Controllers\ImageCoursesController;

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

// mentor routes / endpoint
Route::get('mentors', [MentorsController::class, 'index']);
Route::get('mentors/{id}', [MentorsController::class, 'show']);
Route::post('mentors', [MentorsController::class, 'create']);
Route::put('mentors/{id}', [MentorsController::class, 'update']);
Route::delete('mentors/{id}', [MentorsController::class, 'destroy']);

// course routes / endpoint
Route::get('courses', [CoursesController::class, 'index']);
Route::get('courses/{id}', [CoursesController::class, 'show']);
Route::post('courses', [CoursesController::class, 'create']);
Route::put('courses/{id}', [CoursesController::class, 'update']);
Route::delete('courses/{id}', [CoursesController::class, 'destroy']);

// chapter routes / endpoint
Route::get('chapters', [ChaptersController::class, 'index']);
Route::get('chapters/{id}', [ChaptersController::class, 'show']);
Route::post('chapters', [ChaptersController::class, 'create']);
Route::put('chapters/{id}', [ChaptersController::class, 'update']);
Route::delete('chapters/{id}', [ChaptersController::class, 'destroy']);

// lesson router / endpoint
Route::get('lessons', [LessonsController::class, 'index']);
Route::get('lessons/{id}', [LessonsController::class, 'show']);
Route::post('lessons', [LessonsController::class, 'create']);
Route::put('lessons/{id}', [LessonsController::class, 'update']);
Route::delete('lessons/{id}', [LessonsController::class, 'destroy']);

// image course router / endpoint
Route::post('image-courses', [ImageCoursesController::class, 'create']);
Route::delete('image-courses/{id}', [ImageCoursesController::class, 'destroy']);

// my course / endpoint
Route::get('my-courses', [MyCoursesController::class, 'index']);
Route::post('my-courses', [MyCoursesController::class, 'create']);
Route::post('my-courses/premium', [MyCoursesController::class, 'createPremiumAccess']);

// review / endpoint
Route::post('reviews', [ReviewsController::class, 'create']);
Route::put('reviews/{id}', [ReviewsController::class, 'update']);
Route::delete('reviews/{id}', [ReviewsController::class, 'destroy']);
