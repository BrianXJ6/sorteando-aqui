<?php

namespace App\Contracts\Auth;

use App\Support\ORM\BaseAuthenticable;

interface AuthenticableServiceInterface
{
    /**
     * Handle confirm the password in session
     *
     * @return void
     */
    public function confirmPassword(): void;

    /**
     * Handle password confirmation revocation in session
     *
     * @return void
     */
    public function revokeConfirmPassword(): void;

    /**
     * Callback to handle password recovery request process
     *
     * @param \App\Support\ORM\BaseAuthenticable $user
     * @param string $token
     *
     * @return string
     */
    public function processPasswordRecovery(BaseAuthenticable $user, string $token): string;

    /**
     * Callback to handle password reset request process
     *
     * @param \App\Support\ORM\BaseAuthenticable $user
     * @param string $password
     *
     * @return void
     */
    public function processPasswordReset(BaseAuthenticable $user, string $password): void;
}
