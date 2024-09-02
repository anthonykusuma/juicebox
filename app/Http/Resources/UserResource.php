<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
             * The name of the user.
             */
            'name' => $this->name,

            /**
             * The email of the user.
             */
            'email' => $this->email,

            /**
             * The date and time the user was created.
             */
            'created_at' => $this->created_at,

            /**
             * The date and time the user was last updated.
             */
            'updated_at' => $this->updated_at,
        ];
    }
}
