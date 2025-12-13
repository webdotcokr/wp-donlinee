<?php
/**
 * Plugin Name: 돈린이 수강 신청 시스템 (디버그 모드)
 * Plugin URI: https://donlinee.com
 * Description: 돈마고치 수강 신청 및 대기 신청 통합 관리 시스템
 * Version: 1.0.1
 * Author: Donlinee
 * License: GPL v2 or later
 * Text Domain: donlinee-enrollment
 */

if (!defined('ABSPATH')) exit;

// 경로 상수 정의
define('DONLINEE_ENROLLMENT_VERSION', '1.0.1');
define('DONLINEE_ENROLLMENT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DONLINEE_ENROLLMENT_PLUGIN_URL', plugin_dir_url(__FILE__));

// =================================================================
// 🚨 [긴급 조치] 파일 로드 문제가 의심되므로 여기서 직접 로드 시도
// =================================================================
$files = array(
    'includes/class-database.php',
    'includes/class-settings.php',
    'includes/class-ajax-handler.php',
    'includes/class-admin.php',
    'includes/class-forms.php'
);

foreach ($files as $file) {
    $path = DONLINEE_ENROLLMENT_PLUGIN_DIR . $file;
    if (file_exists($path)) {
        require_once $path;
    } else {
        // 파일이 없으면 에러 로그에 기록
        error_log("[Donlinee Error] Missing file: " . $path);
    }
}

// =================================================================
// 🚨 [긴급 조치] AJAX 핸들러 강제 등록 (클래스 무시하고 직접 등록)
// =================================================================
add_action('wp_ajax_donlinee_switch_mode', 'donlinee_emergency_switch_mode');
function donlinee_emergency_switch_mode() {
    // 1. 요청이 들어왔는지 로그 남기기
    error_log("[Donlinee Debug] Switch Mode Requested");

    // 2. 권한 체크
    if (!current_user_can('manage_options')) {
        wp_send_json_error('권한이 없습니다.');
    }

    // 3. 설정 클래스가 로드되었는지 확인
    if (!class_exists('Donlinee_Enrollment_Settings')) {
        wp_send_json_error('Settings 클래스를 찾을 수 없습니다.');
    }

    // 4. 로직 실행
    $current_settings = Donlinee_Enrollment_Settings::get_current_settings();
    $new_mode = ($current_settings['mode'] === 'enrollment') ? 'waitlist' : 'enrollment';
    
    Donlinee_Enrollment_Settings::update_mode($new_mode);
    
    // 5. 성공 응답
    wp_send_json_success(array(
        'message' => '모드가 변경되었습니다 (Emergency Handler)',
        'mode' => $new_mode
    ));
}

// 나머지 핸들러도 강제 등록 (설정 저장용)
add_action('wp_ajax_donlinee_save_settings', 'donlinee_emergency_save_settings');
function donlinee_emergency_save_settings() {
    if (!current_user_can('manage_options')) wp_send_json_error('No Permission');
    
    // 데이터 수신 확인
    error_log("[Donlinee Debug] Save Settings Data: " . print_r($_POST, true));

    $settings = array(
        'mode' => sanitize_text_field($_POST['mode']),
        'batch_number' => intval($_POST['batch_number']),
        'start_date' => sanitize_text_field($_POST['start_date']),
        'end_date' => sanitize_text_field($_POST['end_date']),
        'auto_switch_date' => sanitize_text_field($_POST['auto_switch_date']),
        'max_capacity' => intval($_POST['max_capacity']),
        'is_active' => isset($_POST['is_active']) ? 'true' : 'false',
        'waitlist_button_text' => sanitize_text_field($_POST['waitlist_button_text']),
        'enrollment_button_text' => sanitize_text_field($_POST['enrollment_button_text']),
        'countdown_text_waitlist' => sanitize_text_field($_POST['countdown_text_waitlist']),
        'countdown_text_enrollment' => sanitize_text_field($_POST['countdown_text_enrollment'])
    );

    Donlinee_Enrollment_Settings::update_settings($settings);
    wp_send_json_success('설정이 저장되었습니다.');
}


// 메인 클래스 실행
class Donlinee_Enrollment {
    private static $instance = null;
    public static function get_instance() {
        if (null === self::$instance) self::$instance = new self();
        return self::$instance;
    }
    private function __construct() {
        $this->init_hooks();
    }
    private function init_hooks() {
        // 기존 훅 유지
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'add_enrollment_popup'));
        
        // 관리자 페이지 실행
        if (is_admin() && class_exists('Donlinee_Enrollment_Admin')) {
            new Donlinee_Enrollment_Admin();
        }
        
        // AJAX 클래스 실행 (혹시 모르니)
        if (class_exists('Donlinee_Enrollment_Ajax')) {
            new Donlinee_Enrollment_Ajax();
        }
    }

    public function enqueue_scripts() {
        // if (!is_front_page() && !is_home() && !is_page(array('application', 'apply', '신청'))) return;

        $settings = Donlinee_Enrollment_Settings::get_current_settings();
        
        wp_enqueue_style('donlinee-popup-styles', DONLINEE_ENROLLMENT_PLUGIN_URL . 'assets/css/waitlist.css', array(), DONLINEE_ENROLLMENT_VERSION);
        wp_enqueue_script('donlinee-enrollment', DONLINEE_ENROLLMENT_PLUGIN_URL . 'assets/js/enrollment.js', array('jquery'), DONLINEE_ENROLLMENT_VERSION, true);

        wp_localize_script('donlinee-enrollment', 'donlinee_enrollment', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('donlinee-enrollment-nonce'),
            'current_mode' => $settings['mode'],
            'batch_number' => $settings['batch_number'],
            'payment_url' => 'https://donmagotchi.cafe24.com/product/project/11/',
            'waitlist_button_text' => $settings['waitlist_button_text'],
            'enrollment_button_text' => $settings['enrollment_button_text'],
            'countdown_text_waitlist' => $settings['countdown_text_waitlist'],
            'countdown_text_enrollment' => $settings['countdown_text_enrollment'],
            'is_active' => $settings['is_active']
        ));
    }

    public function add_enrollment_popup() {
        // if (!is_front_page() && !is_home() && !is_page(array('application', 'apply', '신청'))) return;
        $settings = Donlinee_Enrollment_Settings::get_current_settings();
        if ($settings['mode'] === 'enrollment' && $settings['is_active']) {
            Donlinee_Enrollment_Forms::render_enrollment_popup();
        }
    }
}

Donlinee_Enrollment::get_instance();