<?php

namespace App\Services;

use App\Models\User;
use App\Data\SignInAuthUserData;
use Illuminate\Auth\AuthManager;
use App\Support\ORM\BaseAuthenticable;
use Illuminate\Session\SessionManager;
use Illuminate\Contracts\Auth\PasswordBroker;
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
        $user->generateApiToken();

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

    /**
     * Callback to handle password recovery request process
     *
     * @param BaseAuthenticable $user
     * @param string $token
     *
     * @return string
     */
    public function processPasswordRecovery(BaseAuthenticable $user, string $token): string
    {
        $user->sendPasswordResetNotification($token);

        return PasswordBroker::RESET_LINK_SENT;
    }

    /**
     * Callback to handle password reset request process
     *
     * @param \App\Support\ORM\BaseAuthenticable $user
     * @param string $password
     *
     * @return void
     */
    public function processPasswordReset(BaseAuthenticable $user, string $password): void
    {
        $user->password = $password;
        $user->save();

        if ($this->hasSession()) {
            $this->sessionRegenerate();
            $this->confirmPassword();
        } else {
            $user->generateApiToken();
        }

        $this->authManager->login($user);
    }
}
