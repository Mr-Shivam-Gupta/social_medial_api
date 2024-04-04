<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Auth\LoginRegisterController;

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

// Public routes of authtication
Route::controller(LoginRegisterController::class)->group(function() {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/forgot-password', 'forgotPassword'); //not working
});



// Protected routes
Route::middleware('auth:sanctum')->group( function () {
    Route::post('/logout', [LoginRegisterController::class, 'logout']);

    // Public routes of profiles
    Route::prefix('profiles')->group(function () {

    Route::get('/', [ProfileController::class, 'index']);
    Route::get('/{id}', [ProfileController::class, 'show']);
    //Route::post('/', [ProfileController::class, 'store']); // no need of this route
    Route::put('/{id}/update', [ProfileController::class, 'update']); 
    Route::delete('/{id}/delete', [ProfileController::class, 'destroy']);
    
    Route::put('/{id}/privacy', [ProfileController::class, 'updatePrivacy']);

    Route::post('/{id}/follow', [ProfileController::class, 'followUser']);
    Route::post('/{id}/unfollow', [ProfileController::class, 'unfollowUser']);
    Route::get('/{id}/followers', [ProfileController::class, 'followers']);
    Route::get('/user/following', [ProfileController::class, 'userFollowing']);

    });


    // Like posts
    Route::prefix('posts')->group(function () {

    Route::get('/', [PostController::class, 'index']);
    Route::get('/{id}', [PostController::class, 'show']);
    Route::post('/', [PostController::class, 'store']);
    Route::put('/{id}/update', [PostController::class, 'update']);
    Route::delete('/{id}/delete', [PostController::class, 'destroy']);
    });
    
    // Like routes
    Route::prefix('likes')->group(function () {

    Route::post('/', [LikeController::class, 'store']);
    Route::delete('/{id}', [LikeController::class, 'destroy']);

    });
    
    // Comment routes
    Route::prefix('comments')->group(function () {

    Route::post('/', [CommentController::class, 'store']);
    Route::post('/{id}/reply', [CommentController::class, 'reply']);
    Route::delete('/{id}', [CommentController::class, 'destroy']);

    });

});


