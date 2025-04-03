<?php


namespace App\Helper;

final class HandleApiErrorsHelper
{

    public static function handle($errors): array
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[$error->getPropertyPath()] = $error->getMessage();
        }
        return $errorMessages;
    }
}
