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

// Slack Integration Settings
// Slack Webhook URL을 여기에 입력하세요
// 예: https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXXXXXXXXXX
if(getenv('SLACK_WEBHOOK_URL')) {
    define('SLACK_WEBHOOK_URL', getenv('SLACK_WEBHOOK_URL'));
} else {
    // 환경변수가 없을 경우 직접 입력 (보안상 환경변수 사용 권장)
    define('SLACK_WEBHOOK_URL', ''); // .env 파일 또는 환경변수에서 설정하세요
}

// Slack 알림 활성화 여부
define('SLACK_NOTIFICATIONS_ENABLED', true);

// Slack 채널명 (옵션 - Webhook 설정에서 기본 채널이 정해짐)
define('SLACK_CHANNEL', '#수강신청-알림');

// Enable debug logging - 임시로 항상 활성화
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// Enable debug logging in development
if(getenv('WP_ENV') === 'development') {
    // 이미 위에서 설정됨
}