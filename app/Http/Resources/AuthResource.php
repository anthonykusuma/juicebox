<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The user.
             */
            'user' => new UserResource($this->resource['user']),

            /**
             * The token of the user.
             */
            'token' => $this->resource['token'],
        ];
    }
}
