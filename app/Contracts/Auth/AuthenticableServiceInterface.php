<?php

namespace App\Contracts\Auth;

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
}
