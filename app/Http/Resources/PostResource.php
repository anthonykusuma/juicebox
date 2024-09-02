<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            /**
             * The user who made the post.
             */
            'user_id' => $this->user_id,

            /**
             * The title of the post.
             */
            'title' => $this->title,

            /**
             * The slug of the post that is generated automatically.
             */
            'slug' => $this->slug,

            /**
             * The content of the post.
             */
            'content' => $this->content,

            /**
             * The date and time the post was created.
             */
            'created_at' => $this->created_at,

            /**
             * The date and time the post was last updated.
             */
            'updated_at' => $this->updated_at,
        ];
    }
}
