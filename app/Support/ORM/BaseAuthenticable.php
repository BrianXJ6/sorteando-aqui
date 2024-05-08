<?php

namespace App\Support\ORM;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

abstract class BaseAuthenticable extends Authenticable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasApiTokens;

    /*
    |--------------------------------------------------------------------------
    | Functions
    |--------------------------------------------------------------------------
    */

    /**
     * Generate new token for API access
     *
     * @return string
     */
    public function generateApiToken(): string
    {
        $this->tokens()->where('name', 'api')->delete();
        $this->token = $this->createToken('api')->plainTextToken;

        return $this->token;
    }

    /**
     * Set timestamp value for last login
     *
     * @param boolean $autoSave
     *
     * @return void
     */
    public function setLastLogin(bool $autoSave = true): void
    {
        $this->forceFill(['last_login' => now()]);

        if ($autoSave) {
            $this->save();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    /**
     * Send the password reset notification.
     *
     * @param string $token
     *
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
