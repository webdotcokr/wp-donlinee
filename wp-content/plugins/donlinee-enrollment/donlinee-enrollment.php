<?php
/**
 * Plugin Name: 돈린이 수강 신청 시스템
 * Plugin URI: https://donlinee.com
 * Description: 돈마고치 수강 신청 및 대기 신청 통합 관리 시스템
 * Version: 1.0.0
 * Author: Donlinee
 * License: GPL v2 or later
 * Text Domain: donlinee-enrollment
 */

// 직접 접근 방지
if (!defined('ABSPATH')) {
    exit;
}

// 플러그인 상수 정의
define('DONLINEE_ENROLLMENT_VERSION', '1.0.0');
define('DONLINEE_ENROLLMENT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DONLINEE_ENROLLMENT_PLUGIN_URL', plugin_dir_url(__FILE__));

// 필요한 클래스 파일 로드
require_once DONLINEE_ENROLLMENT_PLUGIN_DIR . 'includes/class-database.php';
require_once DONLINEE_ENROLLMENT_PLUGIN_DIR . 'includes/class-settings.php';
require_once DONLINEE_ENROLLMENT_PLUGIN_DIR . 'includes/class-ajax-handler.php';
require_once DONLINEE_ENROLLMENT_PLUGIN_DIR . 'includes/class-admin.php';
require_once DONLINEE_ENROLLMENT_PLUGIN_DIR . 'includes/class-forms.php';

// 플러그인 메인 클래스
class Donlinee_Enrollment {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        // 플러그인 활성화/비활성화 훅
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // 초기화
        add_action('init', array($this, 'init'));

        // 스크립트 및 스타일 로드
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        // 팝업 HTML 추가
        add_action('wp_footer', array($this, 'add_enrollment_popup'));

        // AJAX 핸들러 초기화
        new Donlinee_Enrollment_Ajax();

        // 관리자 페이지 초기화
        if (is_admin()) {
            new Donlinee_Enrollment_Admin();
        }

        // 자동 모드 전환 크론 설정
        add_action('donlinee_check_mode_switch', array($this, 'check_and_switch_mode'));

        // 크론 스케줄 등록
        if (!wp_next_scheduled('donlinee_check_mode_switch')) {
            wp_schedule_event(time(), 'every_minute', 'donlinee_check_mode_switch');
        }

        // 버튼 텍스트 필터
        add_filter('donlinee_cta_button_text', array($this, 'get_button_text'));
        add_filter('donlinee_cta_button_class', array($this, 'get_button_class'));
    }

    public function activate() {
        // 데이터베이스 테이블 생성
        Donlinee_Enrollment_Database::create_tables();

        // 기본 설정 생성
        Donlinee_Enrollment_Settings::create_default_settings();

        // 크론 이벤트 등록
        if (!wp_next_scheduled('donlinee_check_mode_switch')) {
            wp_schedule_event(time(), 'every_minute', 'donlinee_check_mode_switch');
        }
    }

    public function deactivate() {
        // 크론 이벤트 제거
        wp_clear_scheduled_hook('donlinee_check_mode_switch');
    }

    public function init() {
        // 매분 실행 스케줄 추가
        add_filter('cron_schedules', function($schedules) {
            $schedules['every_minute'] = array(
                'interval' => 60,
                'display' => __('Every Minute')
            );
            return $schedules;
        });
    }

    public function enqueue_scripts() {
        $settings = Donlinee_Enrollment_Settings::get_current_settings();

        // CSS 파일 로드
        wp_enqueue_style(
            'donlinee-enrollment',
            DONLINEE_ENROLLMENT_PLUGIN_URL . 'assets/css/enrollment.css',
            array(),
            DONLINEE_ENROLLMENT_VERSION
        );

        // JavaScript 파일 로드
        wp_enqueue_script(
            'donlinee-enrollment',
            DONLINEE_ENROLLMENT_PLUGIN_URL . 'assets/js/enrollment.js',
            array('jquery'),
            DONLINEE_ENROLLMENT_VERSION,
            true
        );

        // AJAX URL 및 설정 전달
        wp_localize_script('donlinee-enrollment', 'donlinee_enrollment', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('donlinee-enrollment-nonce'),
            'current_mode' => $settings['mode'],
            'batch_number' => $settings['batch_number'],
            'payment_url' => 'https://donmagotchi.cafe24.com/product/%EA%B0%95%EC%A0%9C-%EC%8B%A4%ED%96%89%ED%98%95-%EC%82%AC%EC%97%85-%EA%B0%95%EC%9D%98-%EB%8F%88%EB%A7%88%EA%B3%A0%EC%B9%98-1%EA%B8%B0/11/category/1/display/2/?icid=MAIN.product_listmain_1',
            // 텍스트 설정
            'waitlist_button_text' => $settings['waitlist_button_text'] ?? '수강 대기신청',
            'enrollment_button_text' => $settings['enrollment_button_text'] ?? '(OPEN) 수강 신청하기',
            'countdown_text_waitlist' => $settings['countdown_text_waitlist'] ?? '모집 시작까지',
            'countdown_text_enrollment' => $settings['countdown_text_enrollment'] ?? '모집 마감까지',
            'is_active' => $settings['is_active']
        ));
    }

    public function add_enrollment_popup() {
        $settings = Donlinee_Enrollment_Settings::get_current_settings();

        // 수강신청 모드일 때만 팝업 추가
        if ($settings['mode'] === 'enrollment' && $settings['is_active']) {
            Donlinee_Enrollment_Forms::render_enrollment_popup();
        }
    }

    public function check_and_switch_mode() {
        $settings = Donlinee_Enrollment_Settings::get_current_settings();

        if (!$settings['is_active']) {
            return;
        }

        $current_time = current_time('timestamp');
        $switch_time = strtotime($settings['auto_switch_date']);

        // 자동 전환 시간이 되면 모드 변경
        if ($settings['mode'] === 'waitlist' && $current_time >= $switch_time) {
            Donlinee_Enrollment_Settings::update_mode('enrollment');

            // Slack 알림 발송
            $this->send_slack_notification('수강 신청 모드로 자동 전환되었습니다.');
        }

        // 마감 시간 체크
        $end_time = strtotime($settings['end_date']);
        if ($current_time >= $end_time && $settings['is_active']) {
            Donlinee_Enrollment_Settings::update_setting('is_active', false);

            // Slack 알림 발송
            $this->send_slack_notification('모집이 자동으로 마감되었습니다.');
        }
    }

    public function get_button_text($text) {
        $settings = Donlinee_Enrollment_Settings::get_current_settings();

        if ($settings['mode'] === 'enrollment' && $settings['is_active']) {
            return '수강 신청하기';
        }

        return '수강 대기신청';
    }

    public function get_button_class($class) {
        $settings = Donlinee_Enrollment_Settings::get_current_settings();

        if ($settings['mode'] === 'enrollment' && $settings['is_active']) {
            return 'donlinee-enrollment-trigger';
        }

        return 'donlinee-waitlist-trigger';
    }

    private function send_slack_notification($message) {
        if (defined('SLACK_WEBHOOK_URL') && SLACK_WEBHOOK_URL) {
            $data = array(
                'text' => '[돈마고치 모집 시스템] ' . $message,
                'channel' => defined('SLACK_CHANNEL') ? SLACK_CHANNEL : null
            );

            wp_remote_post(SLACK_WEBHOOK_URL, array(
                'body' => json_encode($data),
                'headers' => array('Content-Type' => 'application/json')
            ));
        }
    }
}

// 플러그인 초기화
Donlinee_Enrollment::get_instance();