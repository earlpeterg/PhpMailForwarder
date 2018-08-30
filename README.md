# PhpMailForwarder

An Automatic IMAP/POP3 Email Forwarder

## About this project

PHP Email Forwarder automatically gets all unread message from a specified IMAP/POP3 email account. Then forwards using PHP Mail() it to another email address or list of email addresses. You may use this for your company email, in which, messages received can be forwarded to multiple employees.

## Setting up

 * Copy the files to a specific directory.
 * Create a copy of `settings.sample.php` and rename it as `settings.php`.
 * Fill in the fields required in the file.
 * An instance of the settings will look like this:

        <?php
        // ... (removed code)
        'in' => [
            'host' => 'imap.yourdomain.com',
            'port' => 995,
            'type' => 'imap', // can be: pop3 or imap, note: seen/unseen does not work on pop3
            'security' => 'ssl' // can be: ssl, tls, notls
        ],

        // username and password for incoming mail box
        'user' => 'user@example.com',
        'password' => 'password',

        // where to send
        'to' => ['new_user@example', 'new_user_2@example.com'],

        'delete_copy' => false // when set to true message will be delete after forwarded

 * You can create more instances to watch another email.
 * Create a cron task to run `/usr/bin/php task.php` every 5 minutes or so.
