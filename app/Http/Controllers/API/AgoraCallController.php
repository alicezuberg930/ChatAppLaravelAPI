<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\UserCallChannel;
use Illuminate\Http\Request;
use App\Services\AgoraCallService;
use BoogieFromZk\AgoraToken\RtcTokenBuilder2;
use Illuminate\Support\Facades\Auth;

class AgoraCallController extends Controller
{

    public function createAgoraMeeting(Request $request)
    {
        try {
            // $userCallChannel = new UserCallChannel();
            // $userCallChannel->caller_id = Auth::id();
            // $userCallChannel->group_id = $request->group_id;
            // $userCallChannel->receiver_id = $request->receiver_id;
            // $userCallChannel->save();

            // $channelName = $request->type == "audio" ? "audio_call_" . $userCallChannel->id : "video_call_" . $userCallChannel->id;
            // $uid = $userCallChannel->caller_id;
            $role = RtcTokenBuilder2::ROLE_PUBLISHER;
            $token = RtcTokenBuilder2::buildTokenWithUid(env('AGORA_APP_ID'), env('APP_CERTIFICATE'), "video_call_15", 4, $role, 86400);

            // $userCallChannel->token = $token;
            // $userCallChannel->channel = $channelName;
            // $userCallChannel->save();
            return response()->json(["data" => $token]);
            // return response()->json(["data" => $userCallChannel->load(['receiver', 'caller', 'group'])], 200);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
