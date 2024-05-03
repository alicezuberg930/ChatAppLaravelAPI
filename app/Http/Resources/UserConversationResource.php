<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'user_id' => $this->user_id,
            'conversation_id' => $this->conversation_id,
            'receiver_id' => $this->receiver_id,
            'aaaa' => "iohwuihear",
            'recent_message' => $this->conversation->recent_message,
            'group' => new GroupResource($this->conversation->recipient_group),
            'receiver' => $this->receiver,
        ];
    }
}
