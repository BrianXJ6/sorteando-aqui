<?php

namespace App\Support\ORM;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

abstract class BaseAuthenticable extends Authenticable
{
    use HasFactory;
    use Notifiable;
    use HasApiTokens;
}
