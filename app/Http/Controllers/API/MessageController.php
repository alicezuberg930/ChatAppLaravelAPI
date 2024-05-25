<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserConversationResource;
use App\Models\Message;
use App\Models\UserConversation;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        try {
            $messages = Message::where('conversation_id', $request->conversation_id)->orderBy('id', 'desc')->simplePaginate($this->paginate);
            if (!count($messages)) {
                return response()->json(["message" => "No messages found"], 500);
            } else {
                return response()->json(array_merge(["message" => "Message fetched successfully"], $messages->toArray()), 200);
            }
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $message = new Message();
            $message->content = $request->content;
            $message->sender_id = Auth::id();
            $message->message_type = $request->message_type;
            $message->conversation_id = intval($request->conversation_id);
            $checkSave = $message->save();
            if ($checkSave) {
                if ($request->medias) {
                    foreach ($request->medias as $media) {
                        $message->clearMediaCollection('medias');
                        $message->addMedia($media)->toMediaCollection('medias');
                    }
                }
                // start finding recipient user to sent notifications to
                $receivingUsers = UserConversation::where([['user_id', Auth::id()], ['conversation_id', $request->conversation_id]])->get();
                if (count($receivingUsers) > 0) {
                    foreach ($receivingUsers as $receivingUser) {
                        $fcmIds = array();
                        $notificationService = new FirebaseNotificationService();
                        if ($receivingUser->conversation->recipient_group != null) {
                            foreach ($receivingUser->conversation->recipient_group->users as $user) {
                                foreach ($user->devices as $device) {
                                    if (!empty($device->fcm_id)) {
                                        $fcmIds[] = $device->fcm_id;
                                    }
                                }
                                $notificationBody = array("body" => $request->content, "title" => $user->name, "sound" => "mySound");
                                $notificationService->sendPushNotification($fcmIds, $notificationBody, array());
                                $fcmIds = [];
                            }
                        }
                        if ($receivingUser->receiver != null) {
                            foreach ($receivingUser->receiver->devices as $device) {
                                if (!empty($device->fcm_id)) {
                                    $fcmIds[] = $device->fcm_id;
                                }
                            }
                            $data = new UserConversationResource(UserConversation::where('conversation_id', $request->conversation_id)->first());
                            $notificationBody = array("body" => $request->content, "title" => $receivingUser->receiver->name, "sound" => "mySound");
                            $notificationService->sendPushNotification($fcmIds, $notificationBody, $data);
                        }
                    }
                }
                return response()->json(["message" => "Message sent successfully", "data" => $message->load('sender')], 200);
            } else {
                return response()->json(["message" => "Unable to send message"], 500);
            }
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }
}
