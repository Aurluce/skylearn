<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

final class Mailer
{
    private PHPMailer $mail;

    public function __construct()
    {
        $config = require BASE_PATH . '/config/mail.php';

        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host       = $config['host'];
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = $config['username'];
        $this->mail->Password   = $config['password'];
        $this->mail->SMTPSecure = $config['encryption'] === 'ssl'
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = $config['port'];
        $this->mail->CharSet    = 'UTF-8';

        $this->mail->setFrom($config['from_email'], $config['from_name']);
        if ($config['reply_to']) {
            $this->mail->addReplyTo($config['reply_to']);
        }
    }

    /**
     * Envoie un e-mail HTML.
     */
    public function send(string $to, string $subject, string $htmlBody): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $htmlBody;
            $this->mail->AltBody = strip_tags($htmlBody);

            return $this->mail->send();
        } catch (MailException $e) {
            error_log('[Mailer] ' . $e->getMessage(), 3, BASE_PATH . '/storage/logs/app.log');
            return false;
        }
    }
}