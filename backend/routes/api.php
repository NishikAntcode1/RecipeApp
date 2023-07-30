<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SavedrecipesController;
use Illuminate\Http\Request;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([

    'middleware' => 'api',
    // 'prefix' => 'auth'

], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/updateProfile', [AuthController::class, 'updateProfile']);
    Route::post('/createPost', [PostController::class, 'createPost']);
    Route::get('/userAllPosts', [PostController::class, 'userAllPosts']);
    Route::post('/deletePost/{id}', [PostController::class, 'deletePost']);
    Route::post('/editPost/{id}', [PostController::class, 'editPost']);
    Route::get('/getPostById/{id}', [PostController::class, 'getPostById']);
    Route::post('/liked/{postId}', [LikeController::class, 'liked']);
    Route::get('/isLikedOrNot/{postId}', [LikeController::class, 'isLikedOrNot']);
    Route::post('/saveRecipe/{postId}', [SavedrecipesController::class, 'saveRecipe']);
    Route::get('/isSavedOrNot/{postId}', [SavedrecipesController::class, 'isSavedOrNot']);
});

Route::get('/getAllCategories', [CategoryController::class, 'getAllCategories']);
Route::get('/getAllUsers', [AuthController::class, 'getAllUsers']);
Route::get('/getRelatedPosts/{postId}', [CategoryController::class, 'getRelatedPosts']);





