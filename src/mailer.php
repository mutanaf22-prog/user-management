<?php
require_once __DIR__ . '/config.php';

function send_email($to_email, $subject, $body_html, $to_name = '') {
    if(defined('USE_SMTP') && USE_SMTP) {
        // SMTP via PHPMailer - only used if you install PHPMailer and set USE_SMTP true.
        if(!file_exists(__DIR__ . '/../vendor/autoload.php')) {
            error_log("PHPMailer requested but autoload not found.");
            return false;
        }
        require_once __DIR__ . '/../vendor/autoload.php';
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port = SMTP_PORT;

            $mail->setFrom(MAIL_FROM, MAIL_NAME);
            $mail->addAddress($to_email, $to_name);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body_html;
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Mailer error: ' . $mail->ErrorInfo);
            return false;
        }
    } else {
        // fallback to PHP mail()
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
        $headers .= "From: ".MAIL_NAME." <".MAIL_FROM.">\r\n";
        return mail($to_email, $subject, $body_html, $headers);
    }
}
