<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\User;
use App\Models\UserFriend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FriendController extends Controller
{
    // index - get my friend & search, update - add friend & accept, delete - reject friend & remove friend, store - send request
    public function index(Request $request)
    {
        try {
            $friendList = UserFriend::with(['friend'])->where('user_id', Auth::id())->get();
            return response()->json(['message' => 'Friend list fetched successfully', 'data' => $friendList], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = new UserFriend();
            $user->user_id = Auth::id();
            $user->friend_id = $request->friend_id;
            $user->status = "0";
            $user->is_request = "1";
            $user->save();

            $friend = new UserFriend();
            $friend->user_id = $request->friend_id;
            $friend->friend_id = Auth::id();

            $friend->status = "0";
            $friend->is_request = "0";
            $friend->save();
            if ($friend && $user) {
                return response()->json(['message' => 'Friend request sent successfully'], 200);
            } else {
                return response()->json(['message' => 'Friend request sent failed'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function acceptRequest(Request $request)
    {
        try {
            $me = UserFriend::where('user_id', Auth::id())->update(['status' => '1']);
            $friend = UserFriend::where('user_id', $request->friend_id)->update(['status' => '1']);
            if ($me && $friend) {
                return response()->json(['message' => 'Friend request accepted successfully'], 200);
            } else {
                return response()->json(['message' => 'Friend request accepted failed'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // reject request or remove friend
    public function rejectOrRemoveFriend(Request $request)
    {
        try {
            $me = UserFriend::where('user_id', Auth::id())->delete();
            $friend = UserFriend::where('user_id', $request->friend_id)->delete();
            if ($me && $friend) {
                return response()->json(['message' => 'Friend removed successfully'], 200);
            } else {
                return response()->json(['message' => 'Friend removed failed'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
