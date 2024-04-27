<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'recent_message' => $this->recent_message,
            'updated_at' => $this->updated_at,
            'group' => new GroupResource($this->recipient_group),
        ];
    }
}
