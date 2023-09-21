<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function getUserMessages(Request $request)
    {
        try {
            $messages = Message::where('conversation_id', $request->get('conversation_id'))
                ->join('users', 'users.id', '=', 'messages.sender_id')
                ->get(['messages.*', 'name']);
            foreach ($messages as $message) {
                $message->photos = json_decode($message->photos);
            }
            if (!count($messages)) {
                $response["status"] = "failed";
                $response["message"] = "no data found";
            } else {
                $response["status"] = "success";
                $response["message"] = "data fetched successfully";
                $response["data"] = $messages;
            }
        } catch (\Exception $e) {
            $response["status"] = "error";
            $response["message"] = $e->getMessage();
        }
        return response()->json($response);
    }

    public function sendMessage(Request $request)
    {
        try {
            $message = new Message();
            $message->content = $request->get('content');
            $message->sender_id = $request->get('sender_id');
            $message->message_type = $request->get('message_type');
            $message->conversation_id = $request->get('conversation_id');
            $photos = $request->file('photos');
            if (!empty($photos)) {
                $filenames = [];
                foreach ($photos as $photo) {
                    $extenstion = $photo->getClientOriginalExtension();
                    $filename = 'conversation_image_' .  time() . '.' . $extenstion;
                    $photo->move(public_path('assets/images/conversation/'), $filename);
                    $filename = asset('assets/images/conversation/') . '/' . $filename;
                    array_push($filenames,  $filename);
                }
                $message->photos = json_encode($filenames);
            }
            if ($message->save() == true) {
                $response["status"] = "success";
                $response["message"] = "message created successfully";
                $message->photos = json_decode($message->photos);
                $response["data"] = $message;
            } else {
                $response["status"] = "failed";
                $response["message"] = "unable to create message";
            }
        } catch (\Exception $e) {
            $response["status"] = "error";
            $response["message"] = $e->getMessage();
        }
        return response()->json($response);
    }

    public function deleteMessage(Request $request)
    {
    }
}
