<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Group;
use App\Models\UserConversation;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\Conversions\Conversion;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        try {
            $groups = Group::all();
            return response()->json(["message" => "Data fetched successfully", "data" => $groups], 400);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 401);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'group_name' => 'required|string',
                ],
            );
            if ($validator->fails()) {
                return response()->json(["message" => ($validator->errors())], 401);
            }
            // DB::beginTransaction();
            // create a new group
            $group = new Group();
            $group->fill($request->all());
            $group->save();
            $conversation = new Conversation();
            $conversation->recipient_group_id = $group->id;
            $conversation->save();
            if ($request->avatar) {
                $group->addMedia($request->file('avatar'))->toMediaCollection('avatar');
            }
            // bind the user to the group
            $userGroup = new UserGroup();
            $userGroup->group_id = $group->id;
            $userGroup->admin_id = $request->admin_id;
            $userGroup->save();
            // add user to a new conversation
            $userConversation = new UserConversation();
            $userConversation->user_id = $request->admin_id;
            $userConversation->conversation_id = $conversation->id;
            return response()->json(['message' => 'Group created successfully'], 200);
        } catch (\Exception $e) {
            // DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $group = Group::find($id);
            if ($request->avatar) {
                $group->clearMediaCollection('avatar');
                $group->addMedia($request->file('avatar'))->toMediaCollection('avatar');
            }
            return response()->json(['message' => 'Group updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function show($id)
    {
        try {
            $group = Group::find($id);
            if ($group) {
                return response()->json(['message' => 'Group details fetched successfully', "data" => $group], 200);
            } else {
                return response()->json(['message' => 'No group found'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function addUserToGroup(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    "group_id" => 'required',
                    "user_id" => 'required',
                ],
            );
            if ($validator->fails()) {
                return response()->json(["message" => ($validator->errors())], 401);
            }
            $userGroup = new UserGroup();
            $userGroup->fill($request->all());
            $userGroup->save();
            return response()->json(['message' => 'User added to group successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }
}
