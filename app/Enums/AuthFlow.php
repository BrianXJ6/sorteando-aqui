<?php

namespace App\Enums;

use App\Traits\Enums\EnumFunctions;

enum AuthFlow: string
{
    use EnumFunctions;

    case WEB = 'web';
    case API = 'api';
}
