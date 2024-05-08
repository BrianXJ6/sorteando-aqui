<?php

namespace App\Traits\Auth;

use App\Enums\AuthFlow;
use App\Data\OutputSignInAuthData;
use App\Exceptions\ValidationException;
use App\Data\InputPasswordForgotResetData;
use App\Data\OutputPasswordForgotResetData;
use Illuminate\Contracts\Auth\PasswordBroker;

trait AuthenticableServiceTrait
{
    /**
     * Handle common login process for any type of authenticable user
     *
     * @param array $credentials
     *
     * @return \App\Data\OutputSignInAuthData
     *
     * @throws \App\Exceptions\ValidationException
     */
    public function signIn(array $credentials): OutputSignInAuthData
    {
        if (!$this->authManager->attempt($credentials)) {
            throw ValidationException::withMessages([__('auth.failed')]);
        }

        /** @var \App\Support\ORM\BaseAuthenticable */
        $user = $this->authManager->user();
        $user->setLastLogin();

        if ($this->hasSession()) {
            $this->sessionRegenerate();
            $this->confirmPassword();
        } else {
            $user->generateApiToken();
        }

        return OutputSignInAuthData::from([
            'user' => $user,
            'flow' => $this->hasSession() ? AuthFlow::WEB : AuthFlow::API,
            'response' => __('messages.users.signin', ['Name' => $user->name]),
        ]);
    }

    /**
     * Handle common logout process for any type of authenticable user
     *
     * @return void
     */
    public function signOut(): void
    {
        if ($this->hasSession()) {
            $this->authManager->logout();
            $this->revokeConfirmPassword();
            $this->sessionRegenerate();
        } else {
            $this->authManager->user()->currentAccessToken()->delete();
        }
    }

    /**
     * Check if session exists
     *
     * @return bool
     */
    protected function hasSession(): bool
    {
        return $this->sessionManager->isStarted();
    }

    /**
     * Handle regeneration sessions
     *
     * @return void
     */
    protected function sessionRegenerate(): void
    {
        $this->sessionManager->regenerate();
        $this->sessionManager->regenerateToken();
    }

    /**
     * Handle common forgot password process for any type of authenticable user
     *
     * @param array $credentials
     *
     * @return string
     *
     * @throws \App\Exceptions\ValidationException
     */
    public function requestPasswordRecovery(array $credentials): string
    {
        $response = $this->passwordManager->broker()
                    ->sendResetLink(
                        $credentials,
                        fn ($user, $token) => method_exists($this, 'processPasswordRecovery')
                            ? $this->processPasswordRecovery($user, $token)
                            : null
                    );

        throw_if(
            ($response !== PasswordBroker::RESET_LINK_SENT),
            ValidationException::withMessages([trans($response)])
        );

        return $response;
    }

    /**
     * Handle common reset forgot password process for any type of authenticable user
     *
     * @param \App\Data\InputPasswordForgotResetData $dto
     *
     * @return \App\Data\OutputPasswordForgotResetData
     *
     * @throws \App\Exceptions\ValidationException
     */
    public function resetForgotPassword(InputPasswordForgotResetData $dto): OutputPasswordForgotResetData
    {
        $response = $this->passwordManager->broker()->reset(
            $dto->toArray(),
            fn ($user, $password) => $this->processPasswordReset($user, $password),
        );

        throw_if(
            ($response !== PasswordBroker::PASSWORD_RESET),
            ValidationException::withMessages([trans($response)])
        );

        return OutputPasswordForgotResetData::from([
            'user' => $this->authManager->user(),
            'flow' => $this->hasSession() ? AuthFlow::WEB : AuthFlow::API,
            'response' => $response,
        ]);
    }
}
