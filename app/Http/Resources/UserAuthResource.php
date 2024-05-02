<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Enums\UserLoginFlow;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $flow = $this->resource['flow'];
        $user = $this->resource['user'];
        $response = ['message' => __('messages.users.signin', ['Name' => $user->name])];

        switch ($flow) {
            case UserLoginFlow::WEB:
                $response = array_merge($this->returnForWeb(), $response);
                break;

            case UserLoginFlow::API:
                $response = array_merge($this->returnForApi($user), $response);
                break;
        }

        return $response;
    }

    /**
     * Specific return for WEB type flow
     *
     * @return array
     */
    protected function returnForWeb(): array
    {
        return ['redirect' => redirect()->intended(route('home'))->getTargetUrl()];
    }

    /**
     * Specific return for API type flow
     *
     * @param User $user
     *
     * @return array
     */
    protected function returnForApi(User $user): array
    {
        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'email_verified_at' => $user->email_verified_at,
                'last_login' => $user->last_login,
            ],
            'token' => $user->token,
        ];
    }
}
