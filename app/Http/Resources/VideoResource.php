<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->video_url,
            'post' => $this->post_id,
            'user' => new UserResource($this->user),
            'type' => 'video',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
