<?php
/* Rename this file as "settings.php" and fill in the fields as required
 * to change to your mail server settings. You can also add more instances
 * for another accounts.
 *
 * Create a cron task to run "php task.php" at least every 5 minutes or so.
 */
$instances[] = [
	'in' => [
		'host' => 'imap.yourdomain.com',
		'port' => 995,
		'type' => '/imap', // can be: pop3 or imap, note: seen/unseen does not work on pop3
		'security' => '/ssl' // can be: ssl, tls, notls
	],
	'out' => [
		// sends using php mail() func
		'phpmail' => [
			// when set to true, the source sender's email will be set as "from" email
			'use_original_email' => true
		]
	],
	
	// username and password for incoming mail box
	'user' => 'user@example.com',
	'password' => 'password',
	
	// where to send
	'to' => ['new_user@example', 'new_user_2@example.com'],
	'cc' => [],
	'bcc' => [],

	'delete_copy' => false // when set to true message will be delete after forwarded
];