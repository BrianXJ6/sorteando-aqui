<?php

namespace App\Enums;

use App\Traits\Enums\EnumFunctions;

enum UserLoginFlow: string
{
    use EnumFunctions;

    case WEB = 'email';
    case API = 'phone';
}
