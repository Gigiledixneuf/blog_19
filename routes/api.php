<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use \App\Http\Controllers\CommentController;
use \App\Http\Controllers\LikeController;
use \App\Http\Controllers\SocialiteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/auth/oauth/{provider}/redirect', [SocialiteController::class, 'redirect']);
Route::get('/auth/oauth/{provider}/callback', [SocialiteController::class, 'callback']);


Route::post('/register', [AuthController::class, 'register']);
Route::get('/all_articles', [ArticleController::class, 'index']);
Route::get('/comments',[CommentController::class, 'index']);

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('articles/{articleId}/likes', LikeController::class);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/comment', [CommentController::class, 'store']);
    Route::apiResource('/articles', ArticleController::class)->except('index');
    Route::apiResource('/categories', CategoryController::class);

});
