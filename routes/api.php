<?php

use App\Http\Controllers\CommentsController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\RatingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FriendsController;
use App\Http\Controllers\MessagesController;

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

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('/friends', [FriendsController::class, 'getFriends']);
    Route::post('/friends/{userId}', [FriendsController::class, 'sendFriendRequest']);
    Route::put('/friends/{userId}', [FriendsController::class, 'acceptRequest']);
    Route::delete('/friends/{userId}', [FriendsController::class, 'removeFriend']);

    Route::get('/messages', [MessagesController::class, 'getAllMessages']);
    Route::get('/messages/{messageId}', [MessagesController::class, 'getMessage']);
    Route::post('/messages/{userId}', [MessagesController::class, 'sendMessage']);
    Route::delete('/messages/{messageId}', [MessagesController::class, 'removeMessage']);

    Route::post('/posts', [PostsController::class, 'sendPost']);

    Route::post('/comments/{postId}', [CommentsController::class, 'sendComment']);

    Route::post('/ratings/{bookId}', [RatingsController::class, 'sendRating']);
});

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/posts', [PostsController::class, 'getAllPosts']);
Route::get('/posts/{postId}', [PostsController::class, 'getPost']);

Route::get('/comments/{postId}', [CommentsController::class, 'getComments']);

Route::get('/ratings/{bookId}', [RatingsController::class, 'getRatings']);
