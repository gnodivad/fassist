<?php

namespace App\Modules\Shared\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'name' => $this->name,
            'fcm' => $this->fcm,
            'avatar_url' => $this->getFirstMedia()->getFullUrl()
        ];
    }
}
