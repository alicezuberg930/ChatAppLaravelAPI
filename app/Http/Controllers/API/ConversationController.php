<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\UserConversationResource;
use App\Models\Conversation;
use App\Models\UserConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    public function store(Request $request)
    {
    }

    public function show($id)
    {
        try {
            $conversation = new UserConversationResource(UserConversation::with('conversation.messages')->where([['user_id', Auth::id()], ['conversation_id', $id]])->first());
            if ($conversation) {
                return response()->json(['message' => 'Conversation details fetched successfully', "data" => $conversation], 200);
            } else {
                return response()->json(['message' => 'No conversation found'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function getUserConversations(Request $request)
    {
        try {
            $myConversations = UserConversationResource::collection(UserConversation::where('user_id', Auth::id())->get());
            if (empty($myConversations)) {
                return response()->json(["message" => "No conversations found"], 500);
            } else {
                return response()->json(["message" => "Conversations fetched successfully", "data" => $myConversations], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["message" =>  $e->getMessage()], 500);
        }
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
