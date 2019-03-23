<?php
/**
 * Plugin Name:  woprsk/WpSMTP
 * Description:  Minimal WordPress Mu Plugin for SMTP
 */

namespace woprsk;

class WP_SMTP
{

    // phpmailer vars.
    public static $WP_MAIL_SMTP_HOST;
    public static $WP_MAIL_SMTP_PORT;
    public static $WP_MAIL_SMTP_SECURE;
    public static $WP_MAIL_SMTP_AUTH;
    public static $WP_MAIL_SMTP_USERNAME;
    public static $WP_MAIL_SMTP_PASSWORD;

    // enable error logging
    public static $WP_MAIL_LOG_ERRORS;

    // instance.
    private static $instance;

    /**
     * Return the instance.
     * @return object instance
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        // setup vars.
        self::$WP_MAIL_SMTP_HOST = WP_MAIL_SMTP_HOST;
        self::$WP_MAIL_SMTP_PORT = defined('WP_MAIL_SMTP_PORT') ? WP_MAIL_SMTP_PORT : 587;
        self::$WP_MAIL_SMTP_SECURE = defined('WP_MAIL_SMTP_SECURE') ? WP_MAIL_SMTP_SECURE : 'tls';
        self::$WP_MAIL_SMTP_AUTH = defined('WP_MAIL_SMTP_AUTH') ? WP_MAIL_SMTP_AUTH : false;
        self::$WP_MAIL_SMTP_USERNAME = defined('WP_MAIL_SMTP_USERNAME') ? WP_MAIL_SMTP_USERNAME : '';
        self::$WP_MAIL_SMTP_PASSWORD = defined('WP_MAIL_SMTP_PASSWORD') ? WP_MAIL_SMTP_PASSWORD : '';
        self::$WP_MAIL_LOG_ERRORS = defined('WP_MAIL_LOG_ERRORS') ? WP_MAIL_LOG_ERRORS : false;

        // Setup all other hooks
        add_action('plugins_loaded', [$this, 'setupHooks']);
    }

    /**
     * add various hooks for woo subscriptions.
     * @return void  binds actions and filters.
     */
    public function setupHooks()
    {
        // hit the wordpress phpmailer instance.
        add_action('phpmailer_init', [$this, 'phpmailerInit']);

        // log mail failures.
        if (self::$WP_MAIL_LOG_ERRORS === true) {
            add_action('wp_mail_failed', [$this, 'logMailFail']);
        }
    }

    /**
     * write the wperror to the log.
     * @param  object  $WP_Error wordpress error object
     * @return void
     */
    public function logMailFail($WP_Error)
    {
        self::writeLog($WP_Error->errors['wp_mail_failed'][0]);
    }

    /**
     * write to log filters
     * @return void
     */
    public static function writeLog($log)
    {
        if (is_array($log) || is_object($log)) {
            error_log(print_r($log, true));
        } else {
            error_log($log);
        }
    }

    /**
     * setup a phpmailer instance for sending mail.
     * @param  object  $phpmailer phpmailer instance
     * @return object             phpmailer instance
     */
    public function phpmailerInit($phpmailer)
    {

        // Define that we are sending with SMTP
        $phpmailer->isSMTP();

        // The hostname of the mail server
        $phpmailer->Host = self::$WP_MAIL_SMTP_HOST;

        // SMTP port number - likely to be 25, 465 or 587
        $phpmailer->Port = self::$WP_MAIL_SMTP_PORT;

        // The encryption system to use - ssl (deprecated) or tls
        $phpmailer->SMTPSecure = self::$WP_MAIL_SMTP_SECURE;

        // if using auth, also set the username and password.
        if (self::$WP_MAIL_SMTP_AUTH !== false) {
            // Use SMTP authentication (true|false)
            $phpmailer->SMTPAuth = self::$WP_MAIL_SMTP_AUTH;

            // Username to use for SMTP authentication
            $phpmailer->Username = self::$WP_MAIL_SMTP_USERNAME;

            // Password to use for SMTP authentication
            $phpmailer->Password = self::$WP_MAIL_SMTP_PASSWORD;
        }
    }

    /**
     * enable this plugin if a constant has been defined.
     * @return object|boolean  the class instance or false
     */
    public static function enable()
    {
        if (defined('WP_MAIL_SMTP_HOST') && !empty(WP_MAIL_SMTP_HOST)) {
            return self::instance();
        }
        return false;
    }
}

/**
 * Instantiate instance during muplugins_loaded action
 * @var [type]
 */
add_action('muplugins_loaded', 'Woprsk\WP_SMTP::enable');
