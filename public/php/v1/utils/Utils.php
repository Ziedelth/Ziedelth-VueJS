<?php

class Utils
{
    static function printResponse(JSONResponse $response)
    {
        http_response_code($response->code);
        echo $response->message;
    }

    static function generateRandomString($length = 25): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}