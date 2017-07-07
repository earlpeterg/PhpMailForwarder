<?php
/* PhpMailForwarder
 * Description: Automatic IMAP/POP3 Email Forwarder
 * Author: EarlPeterG
 * URI: https://github.com/earlpeterg/PhpMailForwarder
 * Version: 0.1.0
 */
include 'settings.php';
include 'vendor/autoload.php';

$mailbox = new PhpImap\Mailbox("{" . $settings['in']['host'] . ":" . $settings['in']['port'] . $settings['in']['type'] . $settings['in']['security'] . "}INBOX", $settings['user'], $settings['password'], __DIR__);

// Read all messaged into an array:
$mailsIds = $mailbox->searchMailbox('UNSEEN');
$deleteCopy = isset($settings['delete_copy']) ? $settings['delete_copy'] : false;

foreach($mailsIds as $mailsId){
	$mail = $mailbox->getMail($mailsId);

	$fromName = $mail->fromName;
	$fromAddress = $mail->fromAddress;
	$subject = $mail->subject;
	$type = empty($mail->textHtml) ? "plain" : "mixed";
	$body = empty($mail->textHtml) ? $mail->textPlain : $mail->textHtml;
	$attachments = $mail->getAttachments();

	echo "==================================\n";
	echo "From: " . $fromName . "<" . $fromAddress . ">\n";
	echo "Subject: " . $subject . "\n";
	echo "Content Type: " . $type . "\n";
	echo "Body Length: " . strlen($body) . "\n";
	echo "No. of Attachments: " . count($attachments) . "\n";

	$mail = new PHPMailer;
	$mail->setFrom($settings['user'], $fromName);
	foreach($settings['to'] as $address){
		$mail->addAddress($address);
	}
	foreach($settings['cc'] as $address){
		$mail->addCC($address);
	}
	foreach($settings['bcc'] as $address){
		$mail->addBCC($address);
	}
	$mail->addReplyTo($fromAddress, $fromName);

	$mail->Subject = $subject;
	$mail->Body = $body;
	$mail->isHTML($type == "mixed");

	foreach ($attachments as $attachment) {
		$mail->addAttachment($attachment->filePath, $attachment->name);
	}

	// Send the message, check for errors
	if (!$mail->send()) {
		echo "Mailer Error: " . $mail->ErrorInfo . "\n";
	} else {
		echo "Message forwarded!\n";

		if ($deleteCopy) {
			// Delete message from imap server
			$mailbox->deleteMail($mailsId);
		}
	}

	// Delete temporary attachments
	foreach ($attachments as $attachment) {
		unlink($attachment->filePath);
	}
}