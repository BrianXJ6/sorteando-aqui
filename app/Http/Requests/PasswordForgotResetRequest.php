<?php

namespace App\Http\Requests;

use App\Support\Http\BaseRequest;
use App\Data\InputPasswordForgotResetData;
use Illuminate\Validation\Rules\Password;

class PasswordForgotResetRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * Get the DTO from this request
     *
     * @return \App\Data\InputPasswordForgotResetData
     */
    public function getDTO(): InputPasswordForgotResetData
    {
        return InputPasswordForgotResetData::from($this->validated());
    }
}
