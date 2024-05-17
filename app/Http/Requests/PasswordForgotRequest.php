<?php

namespace App\Http\Requests;

use App\Support\Http\BaseRequest;

class PasswordForgotRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email']
        ];
    }
}
