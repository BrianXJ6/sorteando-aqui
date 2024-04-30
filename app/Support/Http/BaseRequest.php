<?php

namespace App\Support\Http;

use App\Exceptions\ValidationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class BaseRequest extends FormRequest
{
    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     *
     * @return void
     *
     * @throws \App\Exceptions\ValidationException
     */
    #[\Override]
    protected function failedValidation(Validator $validator): void
    {
        throw (new ValidationException($validator))
                ->errorBag($this->errorBag)
                ->redirectTo($this->getRedirectUrl());
    }
}
