<?php
declare(strict_types=1);

namespace Utility\Mail;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Utility\Core\EnvLoader;

class Mailer
{
    private PHPMailer $mailer;
  
    public function __construct()
    { 
        EnvLoader::LoadEnv();
        
        $this->mailer = new PHPMailer(true);

        // Server settings
        $this->mailer->SMTPDebug = SMTP::DEBUG_OFF;
        $this->mailer->isSMTP();
        $this->mailer->Host       = $_ENV['MAIL_HOST'];
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = $_ENV['MAIL_USERNAME'];
        $this->mailer->Password   = $_ENV['MAIL_PASSWORD'];
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port       = $_ENV['MAIL_PORT'];
    }

    public function sendMail(string $to, string $subject, string $body): bool
    {
        try {
            // Recipients
            $this->mailer->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
            $this->mailer->addAddress($to);

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}");
            return false;
        }
    }
}