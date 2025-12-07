<?php
/**
 * Custom WordPress configuration
 * This file is loaded in wp-config.php
 */

// NHN Cloud API Keys from environment variables
if(getenv('NHN_APP_KEY')) {
    define('NHN_APP_KEY', getenv('NHN_APP_KEY'));
}
if(getenv('NHN_SECRET_KEY')) {
    define('NHN_SECRET_KEY', getenv('NHN_SECRET_KEY'));
}
if(getenv('NHN_SENDER_KEY')) {
    define('NHN_SENDER_KEY', getenv('NHN_SENDER_KEY'));
}

// Enable debug logging in development
if(getenv('WP_ENV') === 'development') {
    define('WP_DEBUG', true);
    define('WP_DEBUG_LOG', true);
    define('WP_DEBUG_DISPLAY', false);
}