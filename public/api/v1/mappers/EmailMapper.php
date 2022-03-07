<?php

use PHPMailer\PHPMailer\PHPMailer;

class EmailMapper
{
    /**
     * @param string $subject
     * @param string $body
     * @param string $address
     * @return bool
     */
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
}