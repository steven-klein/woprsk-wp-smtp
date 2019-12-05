<?php
/**
 * Plugin Name:  woprsk/wp-smtp
 * Description:  Minimal WordPress Drop-in Plugin for SMTP using woprsk/wp-utilities
 * Author  Steven Klein <steve@sklein.io>
 */

add_action('muplugins_loaded', function() {
    if (class_exists('\Woprsk\WP\Mail\SMTP') && defined('WP_MAIL_SMTP_HOST')) {
        (new \Woprsk\WP\Mail\SMTP(
            WP_MAIL_SMTP_HOST,
            defined('WP_MAIL_SMTP_PORT') ? WP_MAIL_SMTP_PORT : 587,
            defined('WP_MAIL_SMTP_AUTH') ? WP_MAIL_SMTP_AUTH : false,
            defined('WP_MAIL_SMTP_USERNAME') ? WP_MAIL_SMTP_USERNAME : '',
            defined('WP_MAIL_SMTP_PASSWORD') ? WP_MAIL_SMTP_PASSWORD : '',
            defined('WP_MAIL_SMTP_SECURE') ? WP_MAIL_SMTP_SECURE : 'tls',
            defined('WP_MAIL_LOG_ERRORS') ? WP_MAIL_LOG_ERRORS : false,
        ))->setupHooks();
    }
}, -9999);
