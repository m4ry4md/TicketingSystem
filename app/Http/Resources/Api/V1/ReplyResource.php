<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReplyResource extends JsonResource
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
            'message' => $this->message,
            'created_at' => $this->created_at->toDateTimeString(),
            'user' => new UserResource($this->whenLoaded('user')),
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
