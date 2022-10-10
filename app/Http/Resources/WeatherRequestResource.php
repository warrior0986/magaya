<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class WeatherRequestResource extends JsonResource
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
            'id' => $this->id,
            'region_name' => $this->whenNotNull($this->region_name),
            'current_conditions' => $this->whenNotNull($this->current_conditions),
            'comments' => $this->whenLoaded('comments', function() {
                return new CommentCollection($this->comments);
            }),
            'status' => $this->whenNotNull($this->status),
        ];
    }
}
