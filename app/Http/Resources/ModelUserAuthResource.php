<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Support\ORM\BaseAuthenticable;
use Illuminate\Http\Resources\Json\JsonResource;

class ModelUserAuthResource extends JsonResource
{
    /**
     * Create a new Model User Auth Resource instance
     *
     * @param \App\Support\ORM\BaseAuthenticable $user
     */
    public function __construct(private BaseAuthenticable $user)
    {
        parent::__construct($user);
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'avatar' => $this->avatar,
                'email_verified_at' => $this->email_verified_at,
                'last_login' => $this->last_login,
            ],
            'token' => $this->when(!empty($this->token), $this->token),
        ];
    }
}
