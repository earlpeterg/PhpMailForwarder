<?php
// Rename this file as "settings.php"
// and fill in the fields as required
// to change to your mail server settings.
// Create a cron task to run "php task.php"
// at least every 5 minutes or so.
$settings = [
	'in' => [
		'host' => 'imap.yourdomain.com',
		'port' => 995,
		'type' => '/pop3', //can be: pop3 or imap
		'security' => '/ssl' //can be: ssl, tls, notls
	],
	'user' => 'user@example.com',
	'password' => 'password',
	
	'to' => ['new_user@example', 'new_user_2@example.com'],
	'cc' => [],
	'bcc' => [],

	'delete_copy' => false // when set to true message will be delete after forwarded
];