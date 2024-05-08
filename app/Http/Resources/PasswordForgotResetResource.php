<?php

namespace App\Http\Resources;

use App\Enums\AuthFlow;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PasswordForgotResetResource extends JsonResource
{
    /**
     * Route where the user will be redirected if the flow is via WEB
     *
     * @var string
     */
    private const ROUTE_NAME = 'home';

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        $response = ['message' => trans($this->response)];

        switch ($this->flow) {
            case AuthFlow::WEB:
                $response = array_merge($this->webReturn(), $response);
                break;

            case AuthFlow::API:
                $response = array_merge($this->apiReturn()->toArray($request), $response);
                break;
        }

        return $response;
    }

    /**
     * Return for WEB
     *
     * @return array
     */
    public function webReturn(): array
    {
        return ['redirect' => route(self::ROUTE_NAME)];
    }

    /**
     * Return for API
     *
     * @return \App\Http\Resources\ModelUserAuthResource
     */
    public function apiReturn(): ModelUserAuthResource
    {
        return ModelUserAuthResource::make($this->user);
    }
}
