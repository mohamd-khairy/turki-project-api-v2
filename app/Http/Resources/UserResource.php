<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            'username' => $this->username,
            "email" => $this->email,
            "mobile_country_code" => $this->mobile_country_code,
            "mobile" => $this->mobile,
            "country_code" => $this->country_code,
            'gender' => $this->gender,
            'age' => $this->age,
            'avatar' => $this->avatar,
            'avatar_thumb' => $this->avatar_thumb,
            'description' => $this->description,
            'mobile_verified_at' => $this->mobile_verified_at,
            'email_verified_at' => $this->email_verified_at
        ];
   
    }
}
