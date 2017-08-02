<?php
include 'settings.php';
include 'vendor/autoload.php';

// Below is a slightly modified excerpt from the PhpMailer test
$mail = new PHPMailer;
$mail->setFrom($instances[0]['user'], 'Php Mail Forwarder Test');
$mail->addAddress($instances[0]['user'], 'Php Mail Forwarder Test');
$mail->Subject = 'PHPMailer mail() test';
$mail->msgHTML(file_get_contents('vendor/phpmailer/phpmailer/examples/contents.html'), 'vendor/phpmailer/phpmailer/examples');

//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
}
else {
    echo "Message sent!";
}
