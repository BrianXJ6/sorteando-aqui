<?php

namespace App\Data;

use App\Support\Data\BaseData;

class InputPasswordForgotResetData extends BaseData
{
    /**
     * Create a new Password Forgot Reset Data instance
     *
     * @param string $token
     * @param string $email
     * @param string $password
     */
    public function __construct(
        public readonly string $token,
        public readonly string $email,
        public readonly string $password,
    ) {
        // ...
    }
}
