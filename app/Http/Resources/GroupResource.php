<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function getUsers($users)
    {
        $userList = [];
        foreach ($users as $user) {
            array_push($userList, $user->user);
        }
        return $userList;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'group_name' => $this->group_name,
            'is_active' => $this->is_active,
            'avatar' => $this->avatar,
            'users' => $this->getUsers($this->users),
        ];
    }
}
