<?php

namespace App\Http\Controllers;

use App\Services\UserAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\SignInResource;
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
     * Login flow
     *
     * @param \App\Http\Requests\SignInAuthUserRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function signIn(SignInAuthUserRequest $request): JsonResource
    {
        $outputSignInDTO = DB::transaction(fn () => $this->userAuthService->signIn($request->getDTO()->credentials()));

        return SignInResource::make($outputSignInDTO);
    }

    /**
     * Logout flow
     *
     * @return JsonResponse
     */
    public function signOut(): JsonResponse
    {
        $this->userAuthService->signOut();

        return new JsonResponse(status:JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * First step to password recovery (Request link)
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
     * Last step to password recovery (reset password)
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
