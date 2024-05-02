<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->type == 'like') {
            return [
                'id' => $this->id,
                'type' => $this->type,
                'user' => new UserResource($this->user),
                'like' => new NotificationLikeResource($this->like),
                'seen' => $this->seen,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ];
        }
        if ($this->type == 'comment') {
            return [
                'id' => $this->id,
                'type' => $this->type,
                'user' => new UserResource($this->user),
                'comment' => new NotificationCommentResource($this->comment),
                'seen' => $this->seen,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ];
        }
        if ($this->type == 'repost') {
            return [
                'id' => $this->id,
                'type' => $this->type,
                'user' => new UserResource($this->user),
                'repost' => new NotificationRepostResource($this->repost),
                'seen' => $this->seen,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ];
        }
        return [];
    }
}
