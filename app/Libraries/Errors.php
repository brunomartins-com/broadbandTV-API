<?php

namespace App\Libraries;


use Illuminate\Validation\Validator;

class Errors
{
    public function getAsJSON(Validator $validator, $defaultMessage = 'Please, check the listed errors.')
    {
        foreach ($validator->messages()->all() as $message)
        {
            $errors[] = $message;
        }

        $response = [
            'status' => false,
            'message' => $defaultMessage,
            'errors' => $errors
        ];

        return json_encode($response);
    }
}