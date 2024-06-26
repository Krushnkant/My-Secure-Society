<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        return [
            'user_code' => $this->user_code,
            'user_id' => $this->user_id,
            'full_name' => $this->full_name,
            'profile_pic' => isset($this->profile_pic_url) ? url($this->profile_pic_url) : "",
            'cover_photo' => isset($this->cover_photo_url) ? url($this->cover_photo_url) : "",
            'mobile_no' => $this->mobile_no,
            'email' => $this->email, 
            'gender' => $this->gender,
            'blood_group' => $this->blood_group,
        ];
    }
}
