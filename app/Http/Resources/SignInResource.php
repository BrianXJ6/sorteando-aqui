<?php

namespace App\Http\Resources;

use App\Enums\AuthFlow;
use Illuminate\Http\Request;
use App\Data\OutputSignInAuthData;
use Illuminate\Http\Resources\Json\JsonResource;

class SignInResource extends JsonResource
{
    /**
     * Route where the user will be redirected if the flow is via WEB
     *
     * @var string
     */
    private const ROUTE_NAME = 'home';

    /**
     * Create a new Sign In Resource instance
     *
     * @param \App\Data\OutputSignInAuthData $outputSignInAuthData
     */
    public function __construct(private OutputSignInAuthData $outputSignInAuthData)
    {
        parent::__construct($this->outputSignInAuthData);
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
        $response = ['message' => $this->response];

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
     * Specific return for WEB flow
     *
     * @return array
     */
    protected function webReturn(): array
    {
        return ['redirect' => redirect()->intended(route(self::ROUTE_NAME))->getTargetUrl()];
    }

    /**
     * Specific return for API flow
     *
     * @return \App\Http\Resources\UserAuthResource
     */
    protected function apiReturn(): UserAuthResource
    {
        return UserAuthResource::make($this->user);
    }
}
