<?php

namespace App\Exceptions;

use Illuminate\Validation\ValidationException as BaseValidationException;

class ValidationException extends BaseValidationException
{
    /**
     * Create an error message summary from the validation errors.
     *
     * @param \Illuminate\Validation\Validator $validator
     *
     * @return string
     */
    #[\Override]
    protected static function summarize($validator): string
    {
        $messages = $validator->errors()->all();

        if (! count($messages) || ! is_string($messages[0])) {
            return $validator->getTranslator()->get('The given data was invalid.');
        }

        $message = $validator->getTranslator()->choice('validator errors', count($messages));

        return $message;
    }
}
