<?php

use App\Http\Controllers\api\ConversationController;
use App\Http\Controllers\api\FriendController;
use App\Http\Controllers\api\GroupController;
use App\Http\Controllers\api\MessageController;
use App\Http\Controllers\api\UserController;
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

Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('get-user-messages/', [MessageController::class, 'getUserMessages']);
    Route::get('delete-message', [MessageController::class, 'deleteMessage']);
    Route::post('send-message', [MessageController::class, 'sendMessage']);

    Route::post('update-user-avatar', [UserController::class, 'updateUserAvatar']);
    Route::put('update-user-status', [UserController::class, 'updateUserStatus']);
    Route::get('search-user', [UserController::class, 'searchUser']);

    Route::get('get-user-friends', [FriendController::class, 'getUserFriends']);

    Route::apiResource('conversation', ConversationController::class);
    Route::get('get-user-conversations', [ConversationController::class, 'getUserConversations']);
    Route::post('create-conversation', [ConversationController::class, 'createConversation']);

    Route::apiResource('/group', GroupController::class);
    Route::post('group/add-user-group', [GroupController::class, 'addUserToGroup']);
});
