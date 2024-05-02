<?php

namespace App\Traits\Auth;

use App\Support\ORM\BaseAuthenticable;
use App\Exceptions\ValidationException;

trait AuthenticableServiceTrait
{
    /**
     * Handle common login process for any type of authenticable user
     *
     * @param array $credentials
     *
     * @return \App\Support\ORM\BaseAuthenticable
     *
     * @throws \App\Exceptions\ValidationException
     */
    protected function signIn(array $credentials): BaseAuthenticable
    {
        if (!$this->authManager->attempt($credentials)) {
            throw ValidationException::withMessages([__('auth.failed')]);
        }

        $user = $this->authManager->user();
        $user->forceFill(['last_login' => now()])->save();

        return $user;
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
}
