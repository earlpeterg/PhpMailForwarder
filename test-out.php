<?php
include 'settings.php';
include 'vendor/autoload.php';

//Create a new PHPMailer instance
$mail = new PHPMailer;
//Set who the message is to be sent from
$mail->setFrom($settings['user'], 'First Last');
//Set who the message is to be sent to
$mail->addAddress('earl@earlpeter.com', 'Earl Peter G');
//Set the subject line
$mail->Subject = 'PHPMailer mail() test';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->msgHTML(file_get_contents('vender/phpmailer/phpmailer/examples/contents.html'), dirname(__FILE__));

//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}
