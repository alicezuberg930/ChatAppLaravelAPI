<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    public function index(Request $request)
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

    public function store(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'group_name' => 'required',
                ],
            );

            if ($validator->fails()) {
                return response()->json([
                    "status" => "failed",
                    "message" => $this->readalbeError($validator),
                ], 400);
            }
            $group = new Group();
            $group->fill($request->all());
            if (($request->avatar)) {
                $group->addMedia($request->file('avatar'))->toMediaCollection('avatar');
            }
        } catch (\Exception $e) {
        }
    }
}
