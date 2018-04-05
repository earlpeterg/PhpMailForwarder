<?php
class Mail_Forwarder {
	protected $imap_connection, $recipient, $delete_copy;

	public function __construct($settings) {
		$server = '{'."{$settings['in']['host']}:{$settings['in']['port']}/{$settings['in']['type']}/{$settings['in']['security']}".'}INBOX';
		$user = $settings['user'];
		$password = $settings['password'];
		$attachmentsDir = __DIR__;
		$serverEncoding = 'UTF-8';
		$this->imap_connection = new Mailbox( $server, $user, $password, $attachmentsDir, $serverEncoding );

		$this->sourceMail = $user;
		$this->recipientMails = $settings['to'];

		$this->delete_copy = isset( $settings['delete_copy'] ) ? $settings['delete_copy'] : false;
	}

	public function begin_forward_messages() {
		$uids = $this->get_message_uids();

		foreach( $uids as $uid ) {
			$message = $this->get_message( $uid );

			if ( $this->forward_message( $message ) ) {
				// Delete message from imap server
				if ( $this->delete_copy ) {
					$this->imap_connection->deleteMail( $uid );
				}
			}
			$this->unlink_attachments( $message->attachments );
		}
	}
	
	public function get_message_uids( $criteria = 'UNSEEN' ) {
		return $this->imap_connection->searchMailbox( $criteria );
	}

	public function get_message( $uid ) {
		$mail = $this->imap_connection->getMail( $uid );

		return (Object)[
			'from_name' => $this->toUTF8( $mail->fromName ),
			'from_mail'	=> $mail->fromAddress,
			'reply_to' => $mail->replyTo,

			'subject' => $this->toUTF8( $mail->subject ),

			'text' => $this->toUTF8( $mail->textPlain ),
			'html' => $this->toUTF8( $mail->textHtml ),

			'attachments' => $mail->getAttachments()
		];
	}

	public function forward_message ( $message ) {
		echo "==================================\n";
		echo "From: {$message->from_name} <{$message->from_mail}>\n";
		echo "Subject: {$message->subject}\n";
		echo "No. of Attachments: ".count( $message->attachments )."\n";

		foreach( $this->recipientMails as $recipientMail ){
			if ( !$this->send_message( $message, $recipientMail ) ) {
				return false;
			}
		}

		return true;
	}

	private function send_message( $message, $address ) {
		$mail = new PHPMailer;
		$mail->CharSet = 'UTF-8';
		$mail->Encoding	= 'base64';

		$mail->setFrom( $message->from_mail, $message->from_name );
		$mail->addCustomHeader( 'X-Original-To', $this->sourceMail );
		$mail->addAddress( $address );

		foreach( $message->reply_to as $reply_to_address => $reply_to_name ) {
			$mail->addReplyTo( $reply_to_address, $reply_to_name );
		}

		
		$mail->Subject = $message->subject;
		if( !empty( $message->html ) && !empty( $message->text ) ) {
			$mail->Body = $message->html;
			$mail->AltBody = $message->text;
		} else if ( !empty( $message->html ) && empty( $message->text ) ) {
			$mail->Body = $message->html;
		} else if ( empty( $message->html ) && !empty( $message->text ) ) {
			$mail->Body = $message->text;
		}
		$mail->isHTML( !empty( $message->html ) );
		

		foreach ( $message->attachments as $attachment ) {
			$mail->addAttachment( $attachment->filePath, $attachment->name );
		}

		// Send the message, check for errors
		if ( !$mail->send() ) {
			echo "Mailer Error: {$mail->ErrorInf}.\n";
		}
		else {
			echo "Message forwarded to $address.\n";
			return true;
		}

		return false;
	}

	private function unlink_attachments( $attachments ){
		foreach ( $attachments as $attachment ) {
			unlink($attachment->filePath);
		}
	}
	
	private function toUTF8($str){
		return Encoding::toUTF8($str);
	}
}

class Encoding extends \ForceUTF8\Encoding { }
class Mailbox extends PhpImap\Mailbox { }
