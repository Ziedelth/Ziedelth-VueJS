<?php

use PHPMailer\PHPMailer\PHPMailer;

class EmailMapper
{
    /**
     * Send an email using PHP
     *
     * @param string $subject The subject of the email.
     * @param string $body The body of the email.
     * @param string $address The email address to send the email to.
     *
     * @return bool A boolean value.
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