<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FriendController extends Controller
{
    public function getUserFriends(Request $request)
    {
        try {
            $myFriends = DB::table('user_friend')
                ->join('users', 'users.id', '=', 'user_friend.friend_id')
                ->where('user_friend.user_id', '=', $request->get('user_id'))
                ->get(['user_id', 'friend_id', 'conversation_id', 'name', 'email', 'avatar', 'user_friend.status as friend_status', 'users.status as user_status']);
            if (!count($myFriends)) {
                $response["status"] = "failed";
                $response["message"] = "no data found";
            } else {
                $response["status"] = "success";
                $response["message"] = "data fetched successfully";
                $response["data"] = $myFriends;
            }
        } catch (\Exception $e) {
            $response["status"] = "error";
            $response["message"] = $e->getMessage();
        }
        return response()->json($response);
    }

    public function createConversation(Request $request)
    {
        try {
            $conversation = new Conversation();
            $conversation->type = $request->get('type');
            $conversation->recent_message = $request->get('recent_message');
            $conversation->recent_sender = $request->get('recent_sender');
            if ($conversation->save() == true) {
                $response["status"] = "success";
                $response["message"] = "Conversation created successfully";
                $response["data"] = $conversation;
            } else {
                $response["status"] = "failed";
                $response["message"] = "Unable to create conversation";
            }
        } catch (\Exception $e) {
            $response["status"] = "error";
            $response["message"] = $e->getMessage();
        }
        return response()->json($response);
    }
}
