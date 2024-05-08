<?php

namespace App\Http\Controllers;

use App\Enums\UserLoginFlow;
use App\Services\UserAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserAuthResource;
use App\Http\Requests\PasswordForgotRequest;
use App\Http\Requests\SignInAuthUserRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Requests\PasswordForgotResetRequest;
use App\Http\Resources\PasswordForgotResetResource;

class UserAuthController extends Controller
{
    /**
     * Create a new User Auth Controller instance
     *
     * @param \App\Services\UserAuthService $userAuthService
     */
    public function __construct(private UserAuthService $userAuthService)
    {
        //
    }

    /**
     * Login flow from the WEB
     *
     * @param \App\Http\Requests\SignInAuthUserRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function signInWeb(SignInAuthUserRequest $request): JsonResource
    {
        $user = DB::transaction(fn () => $this->userAuthService->signInWeb($request->getDTO()));

        return UserAuthResource::make(['user' => $user, 'flow' => UserLoginFlow::WEB]);
    }

    /**
     * Login flow from the API
     *
     * @param \App\Http\Requests\SignInAuthUserRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function signInApi(SignInAuthUserRequest $request): JsonResource
    {
        $user = DB::transaction(fn () => $this->userAuthService->signInApi($request->getDTO()));

        return UserAuthResource::make(['user' => $user, 'flow' => UserLoginFlow::API]);
    }

    /**
     * Logout flow from the WEB
     *
     * @return JsonResponse
     */
    public function signOut(): JsonResponse
    {
        $this->userAuthService->signOut();

        return new JsonResponse(status:JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * First step for forgot password flow (request link)
     *
     * @param \App\Http\Requests\PasswordForgotRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function passwordForgotRequest(PasswordForgotRequest $request): JsonResponse
    {
        $response = DB::transaction(fn () => $this->userAuthService->requestPasswordRecovery($request->only('email')));

        return new JsonResponse(['message' => trans($response)]);
    }

    /**
     * Last step for forgot password flow (reset password)
     *
     * @param \App\Http\Requests\PasswordForgotResetRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function passwordForgotReset(PasswordForgotResetRequest $request): JsonResource
    {
        $dto = DB::transaction(fn () => $this->userAuthService->resetForgotPassword($request->getDTO()));

        return PasswordForgotResetResource::make($dto);
    }
}
