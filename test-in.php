<?php
include 'settings.php';
include 'vendor/autoload.php';

foreach($instances as $settings){
	// Connecting to POP3 email server.
	$mailbox = new PhpImap\Mailbox("{" . $settings['in']['host'] . ":" . $settings['in']['port'] . $settings['in']['type'] . $settings['in']['security'] . "}", $settings['user'], $settings['password'], __DIR__);

	// Read all messaged into an array:
	$mailsIds = $mailbox->searchMailbox('ALL');

	// Total number of messages in Inbox
	$count = count($mailsIds);
	echo $count . " message(s) found\n";
}