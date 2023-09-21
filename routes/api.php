<?php

use App\Http\Controllers\api\v1\ConversationController;
use App\Http\Controllers\api\v1\FriendController;
use App\Http\Controllers\api\v1\MessageController;
use App\Http\Controllers\api\v1\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('v1/get-user-messages/', [MessageController::class, 'getUserMessages']);
Route::get('v1/delete-message', [MessageController::class, 'deleteMessage']);
Route::post('v1/send-message', [MessageController::class, 'sendMessage']);

Route::post('v1/login', [UserController::class, 'login']);
Route::post('v1/register', [UserController::class, 'register']);
Route::post('v1/update-user-avatar', [UserController::class, 'updateUserAvatar']);
Route::put('v1/update-user-status', [UserController::class, 'updateUserStatus']);
Route::get('v1/search-user', [UserController::class, 'searchUser']);

Route::get('v1/get-user-friends', [FriendController::class, 'getUserFriends']);

Route::get('v1/get-user-conversations', [ConversationController::class, 'getUserConversations']);
Route::post('v1/create-conversation', [ConversationController::class, 'createConversation']);
