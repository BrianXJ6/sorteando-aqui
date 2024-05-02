<?php

namespace App\Http\Requests;

use App\Enums\UserLoginOption;
use Illuminate\Validation\Rule;
use App\Data\SignInAuthUserData;
use App\Support\Http\BaseRequest;

class SignInAuthUserRequest extends BaseRequest
{
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if (
            ($this->has('option') && $this->has('login')) &&
            ($this->option === UserLoginOption::PHONE->value || $this->option === UserLoginOption::CPF->value)
        ) {
            $this->merge(['login' => dd(onlyNumbers($this->login))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'option' => ['required', Rule::enum(UserLoginOption::class)],
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Get the DTO from this request
     *
     * @return \App\Data\SignInAuthUserData
     */
    public function getDTO(): SignInAuthUserData
    {
        return SignInAuthUserData::from($this->validated());
    }
}
