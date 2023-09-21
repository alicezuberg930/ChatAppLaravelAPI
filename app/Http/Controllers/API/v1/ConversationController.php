<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    public function getUserConversations(Request $request)
    {
        try {
            $myConversations = DB::table('user_conversation')
                ->join('conversations', 'conversations.id', '=', 'user_conversation.conversation_id')
                ->leftjoin('users', 'user_conversation.receiver_id', '=', 'users.id')
                ->where('user_conversation.user_id', '=', $request->get('user_id'))
                ->get(['status', 'user_id', 'conversation_id', 'type', 'recent_message', 'recent_sender', 'name as receiver_name', 'group_name', 'avatar as user_avatar', 'group_avatar']);
            if (!count($myConversations)) {
                $response["status"] = "failed";
                $response["message"] = "no data found";
            } else {
                $response["status"] = "success";
                $response["message"] = "data fetched successfully";
                $response["data"] = $myConversations;
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
