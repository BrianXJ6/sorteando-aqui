<?php

namespace App\Enums;

use App\Traits\Enums\EnumFunctions;

enum UserLoginOption: string
{
    use EnumFunctions;

    case EMAIL = 'email';
    case PHONE = 'phone';
    case CPF = 'cpf';
}
