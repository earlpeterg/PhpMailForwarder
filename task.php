<?php
/* PhpMailForwarder
 * Description: Automatic IMAP/POP3 Email Forwarder
 * Author: EarlPeterG
 * URI: https://github.com/earlpeterg/PhpMailForwarder
 * Version: 0.1.0
 */

/* ---------
TODO:
1) Utilize classes
2) Support for SMTP
3) Improve code readability
--------- */

include 'settings.php';
include 'vendor/autoload.php';
use \ForceUTF8\Encoding;


foreach($instances as $settings){
	$mailbox = getNewMailbox("{" . $settings['in']['host'] . ":" . $settings['in']['port'] . $settings['in']['type'] . $settings['in']['security'] . "}", $settings['user'], $settings['password']);

	// Read all messaged into an array:
	$mailsIds = $mailbox->searchMailbox('UNSEEN');
	$deleteCopy = isset($settings['delete_copy']) ? $settings['delete_copy'] : false;

	foreach($mailsIds as $mailsId){
		$mail = $mailbox->getMail($mailsId);

		$fromName		= toUTF8($mail->fromName);
		$fromAddress	= $mail->fromAddress;

		$subject = toUTF8($mail->subject);

		$type = empty($mail->textHtml) ? "plain" : "mixed";
		$body = toUTF8( empty($mail->textHtml) ? $mail->textPlain : $mail->textHtml );
		$attachments = $mail->getAttachments();

		echo "==================================\n";
		echo "From: $fromName <$fromAddress>\n";
		echo "Subject: $subject\n";
		echo "Content Type: $type\n";
		echo "Body Length: " . strlen($body) . "\n";
		echo "No. of Attachments: " . count($attachments) . "\n";

		$mail = new PHPMailer;
		$mail->CharSet	= 'UTF-8';
		$mail->Encoding	= "base64";

		if ($settings['out']['phpmail']['use_original_email']){
			$mail->setFrom($fromAddress, $fromName);
		}
		else {
			$mail->setFrom($settings['user'], $fromName);
			$mail->addReplyTo($fromAddress, $fromName);
		}
		

		foreach($settings['to'] as $address){
			$mail->addAddress($address);
		}

		foreach($settings['cc'] as $address){
			$mail->addCC($address);
		}

		foreach($settings['bcc'] as $address){
			$mail->addBCC($address);
		}

		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->isHTML($type == "mixed");

		foreach ($attachments as $attachment) {
			$mail->addAttachment($attachment->filePath, $attachment->name);
		}

		// Send the message, check for errors
		if (!$mail->send()) {
			echo "Mailer Error: " . $mail->ErrorInfo . "\n";
		}
		else {
			echo "Message forwarded!\n";

			if ($deleteCopy) {
				// Delete message from imap server
				$mailbox->deleteMail($mailsId);
			}
		}

		// Delete temporary attachments
		unlinkAttachments($attachments);
	}
}

// ---- functions

function toUTF8($str){ return Encoding::toUTF8($str); }
function unlinkAttachments($attachments){
	foreach ($attachments as $attachment) {
		unlink($attachment->filePath);
	}
}
function getNewMailbox($imapPath, $login, $password, $attachmentsDir = __DIR__, $serverEncoding = 'UTF-8'){ return new PhpImap\Mailbox($imapPath, $login, $password, $attachmentsDir, $serverEncoding); }
