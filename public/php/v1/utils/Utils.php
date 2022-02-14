<?php

use PHPMailer\PHPMailer\PHPMailer;

class Utils
{
    static function getPDO(): PDO
    {
        $pdo = new PDO("mysql:host=" . DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
        return $pdo;
    }

    static function sendEmail(string $subject, string $body, string $address): bool
    {
        $phpMailer = new PHPMailer();
        $phpMailer->isSMTP();
        $phpMailer->SMTPAuth = true;
        $phpMailer->Port = 587;
        $phpMailer->CharSet = 'UTF-8';
        $phpMailer->Host = EMAIL_HOST;
        $phpMailer->Username = EMAIL_USERNAME;
        $phpMailer->Password = EMAIL_PASSWORD;

        $phpMailer->isHTML(true);
        $phpMailer->Subject = $subject;
        $phpMailer->Body = $body;

        try {
            $phpMailer->setFrom(EMAIL_USERNAME, "Ziedelth.fr - Ne pas répondre");
            $phpMailer->addAddress($address);

            if (!$phpMailer->send())
                return false;

            return true;
        } catch (Exception $e) {
        }

        return false;
    }

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