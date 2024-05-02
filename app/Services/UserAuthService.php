<?php

namespace App\Services;

use App\Models\User;
use App\Data\SignInAuthUserData;
use Illuminate\Auth\AuthManager;
use Illuminate\Session\SessionManager;
use App\Traits\Auth\AuthenticableServiceTrait;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use App\Contracts\Auth\AuthenticableServiceInterface;

class UserAuthService implements AuthenticableServiceInterface
{
    use AuthenticableServiceTrait;

    /**
     * Create a new User Auth Service instance
     *
     * @param \Illuminate\Auth\AuthManager $authManager
     * @param \Illuminate\Session\SessionManager $sessionManager
     * @param \Illuminate\Auth\Passwords\PasswordBrokerManager $passwordManager
     */
    public function __construct(
        private AuthManager $authManager,
        private SessionManager $sessionManager,
        private PasswordBrokerManager $passwordManager,
    ) {
        $this->authManager->setDefaultDriver('user');
        $this->passwordManager->setDefaultDriver('users');
    }

    /**
     * Login flow from the WEB
     *
     * @param \App\Data\SignInAuthUserData $dto
     *
     * @return \App\Models\User
     */
    public function signInWeb(SignInAuthUserData $dto): User
    {
        /** @var \App\Models\User */
        $user = $this->signIn($dto->credentials());

        if ($this->hasSession()) {
            $this->sessionRegenerate();
            $this->confirmPassword();
        }

        return $user;
    }

    /**
     * Login flow from the API
     *
     * @param \App\Data\SignInAuthUserData $dto
     *
     * @return \App\Models\User
     */
    public function signInApi(SignInAuthUserData $dto): User
    {
        /** @var \App\Models\User */
        $user = $this->signIn($dto->credentials());
        $user->tokens()->where('name', 'api')->delete();
        $user->token = $user->createToken('api')->plainTextToken;

        return $user;
    }

    /**
     * Handle confirm the password in session
     *
     * @return void
     */
    public function confirmPassword(): void
    {
        $this->sessionManager->put('auth.user.password_confirmed_at', time());
    }

    /**
     * Handle password confirmation revocation in session
     *
     * @return void
     */
    public function revokeConfirmPassword(): void
    {
        $this->sessionManager->forget('auth.user');
    }
}
