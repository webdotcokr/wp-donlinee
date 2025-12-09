<?php
/**
 * 돈린이 수강대기 플러그인 설정 파일
 *
 * 이 파일은 플러그인 전용 설정을 담고 있습니다.
 * wp-config-custom.php 설정을 덮어쓰지 않고 보완합니다.
 */

// 직접 접근 방지
if (!defined('ABSPATH')) {
    exit;
}

// Slack 설정이 아직 정의되지 않았다면 여기서 정의
if (!defined('SLACK_WEBHOOK_URL')) {
    // Webhook URL - 환경변수에서 가져오기 (보안상 권장)
    define('SLACK_WEBHOOK_URL', getenv('SLACK_WEBHOOK_URL') ?: '');
}

if (!defined('SLACK_NOTIFICATIONS_ENABLED')) {
    // Slack 알림 활성화 여부
    define('SLACK_NOTIFICATIONS_ENABLED', true);
}

if (!defined('SLACK_CHANNEL')) {
    // Slack 채널 (옵션 - Webhook 설정에서 기본 채널이 정해짐)
    define('SLACK_CHANNEL', '#수강신청-알림');
}

/**
 * 플러그인 전용 추가 설정
 */

// 수강대기 알림 제목 커스터마이징
if (!defined('SLACK_WAITLIST_TITLE')) {
    define('SLACK_WAITLIST_TITLE', '📋 새로운 수강 대기 신청이 접수되었습니다!');
}

// 수강대기 알림 색상 (HEX 코드)
if (!defined('SLACK_WAITLIST_COLOR')) {
    define('SLACK_WAITLIST_COLOR', '#FFA500'); // Orange
}

// 디버깅 모드 (로그 상세 출력) - 문제 해결을 위해 일시적으로 활성화
if (!defined('SLACK_DEBUG_MODE')) {
    define('SLACK_DEBUG_MODE', true);  // 디버그 모드 활성화
}