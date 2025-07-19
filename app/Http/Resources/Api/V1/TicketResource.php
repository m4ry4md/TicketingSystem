<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'title' => $this->title,
            'message' => $this->message,
            'status' => $this->status->value,
            'created_at' => $this->created_at->toDateTimeString(),
            'user' => new UserResource($this->whenLoaded('user')),
            'replies' => ReplyResource::collection($this->whenLoaded('replies')),
            'attachments' => $this->getMedia('attachments')->map(function ($media) {
                return [
                    'name' => $media->name,
                    'mime_type' => $media->mime_type,
                    'download_url' => route('attachments.show', $media),
                    'inline_url' => \Illuminate\Support\Str::startsWith($media->mime_type, 'image/')
                        ? route('attachments.show', ['media' => $media, 'inline' => true])
                        : null,
                ];
            }),
            'sender_type' => $this->sender_type->value,
        ];
    }
}
