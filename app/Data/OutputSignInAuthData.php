<?php

namespace App\Data;

use App\Enums\AuthFlow;
use App\Support\Data\BaseData;
use App\Support\ORM\BaseAuthenticable;

class OutputSignInAuthData extends BaseData
{
    /**
     * Create a new Output SignIn Auth Data instance
     *
     * @param \App\Support\ORM\BaseAuthenticable $user
     * @param \App\Enums\AuthFlow $flow
     * @param string $response
     */
    public function __construct(
        public readonly BaseAuthenticable $user,
        public readonly AuthFlow $flow,
        public readonly string $response,
    ) {
        // ...
    }
}
