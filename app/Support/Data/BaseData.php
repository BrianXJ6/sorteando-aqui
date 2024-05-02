<?php

namespace App\Support\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
#[MapInputName(SnakeCaseMapper::class)]
abstract class BaseData extends data
{
    // ...
}
