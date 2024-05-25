<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserConversationResource;
use App\Models\Conversation;
use App\Models\UserConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function index()
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

    public function store(Request $request)
    {
        // one to one chat
        try {
            // DB::beginTransaction();
            $conversation = new Conversation();
            $conversation->recent_message = $request->recent_message;
            $conversation->save();

            $userConversation = new UserConversation();
            $userConversation->user_id = Auth::id();
            $userConversation->conversation_id = $conversation->id;
            $userConversation->receiver_id = $request->receiver_id;
            $userConversation->save();

            $userConversation2 = new UserConversation();
            $userConversation2->user_id = $request->receiver_id;
            $userConversation2->conversation_id = $conversation->id;
            $userConversation2->receiver_id = Auth::id();
            $userConversation2->save();

            // if ($checkConversation && $checkUserConversation && $checkUserConversation2) {
            return response()->json(["message" => "Conversation created successfully", "data" => new UserConversationResource($userConversation)], 200);
            // } else {
            // DB::rollBack();
            // return response()->json(["message" => "Unable to create conversation"], 500);
            // }
        } catch (\Exception $e) {
            // DB::rollBack();
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $conversation = new UserConversationResource(UserConversation::where([['user_id', Auth::id()], ['conversation_id', $id]])->first());
            if ($conversation) {
                return response()->json(['message' => 'Conversation details fetched successfully', "data" => $conversation], 200);
            } else {
                return response()->json(['message' => 'No conversation found'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function checkForConversationWithUser(Request $request)
    {
        try {
            $myConversation = (UserConversation::where([['user_id', Auth::id()], ['receiver_id', $request->receiver_id]])->first());
            $recipientConversation = (UserConversation::where([['user_id', $request->receiver_id], ['receiver_id', Auth::id()]])->first());
            if ($myConversation && $recipientConversation) {
                return response()->json(['message' => 'Conversation fetched successfully', 'data' => new UserConversationResource($myConversation)], 200);
            } else {
                return response()->json(['message' => 'No conversation found'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
