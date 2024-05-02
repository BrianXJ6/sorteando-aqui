<?php

namespace App\Data;

use App\Enums\UserLoginOption;
use App\Support\Data\BaseData;

class SignInAuthUserData extends BaseData
{
    /**
     * Create a new Sign In Auth User Data instance
     *
     * @param \App\Enums\UserLoginOption $option
     * @param string $login
     * @param string $password
     */
    public function __construct(
        public readonly UserLoginOption $option,
        public readonly string $login,
        public readonly string $password,
    ) {
        // ...
    }

    /**
     * Get credentials for sign in
     *
     * @return array
     */
    public function credentials(): array
    {
        return array_merge([$this->option->value => $this->login], $this->only('password')->toArray());
    }
}
