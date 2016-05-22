<?php

namespace App\Libraries;


use Illuminate\Validation\Validator;

class Errors
{
    public function getAsJSON(Validator $validator, $defaultMessage = 'Please, check the listed erros.')
    {
        foreach ($validator->messages()->all() as $message)
        {
            $errors[] = $message;
        }

        $response = [
            'status' => false,
            'message' => $defaultMessage,
            'erros' => $errors
        ];

        return json_encode($response);
    }
}