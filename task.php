<?php
/* PhpMailForwarder
 * Description: Automatic IMAP/POP3 Email Forwarder
 * Author: EarlPeterG
 * URI: https://github.com/earlpeterg/PhpMailForwarder
 * Version: 0.2.0
 */

// TODO: Support SMTP
// TODO: Support logging
include 'settings.php';
include 'vendor/autoload.php';
include 'mail_forwarder.php';

foreach( $instances as $instance ) {
	$mail_forwarder = new Mail_Forwarder( $instance );
	$mail_forwarder->begin_forward_messages();
}
