<?php
/**
 * Plugin Name: 돈린이 수강 대기 신청
 * Plugin URI: https://donlinee.com
 * Description: 돈마고치 수강 대기 신청 폼 플러그인 (알림톡 발송용)
 * Version: 1.0.0
 * Author: Donlinee
 * License: GPL v2 or later
 * Text Domain: donlinee-waitlist
 */

// 직접 접근 방지
if (!defined('ABSPATH')) {
    exit;
}

// 플러그인 상수 정의
define('DONLINEE_WAITLIST_VERSION', '1.0.0');
define('DONLINEE_WAITLIST_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DONLINEE_WAITLIST_PLUGIN_URL', plugin_dir_url(__FILE__));

// wp-config-custom.php 파일 로드 (있는 경우)
$custom_config_file = ABSPATH . '../wp-config-custom.php';
if (file_exists($custom_config_file)) {
    require_once $custom_config_file;
}

// 플러그인 설정 파일 로드
require_once DONLINEE_WAITLIST_PLUGIN_DIR . 'config.php';

// 필요한 클래스 파일 로드
require_once DONLINEE_WAITLIST_PLUGIN_DIR . 'includes/class-database.php';
require_once DONLINEE_WAITLIST_PLUGIN_DIR . 'includes/class-ajax-handler.php';
require_once DONLINEE_WAITLIST_PLUGIN_DIR . 'includes/class-admin.php';

// 플러그인 메인 클래스
class Donlinee_Waitlist {

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
        add_action('wp_footer', array($this, 'add_popup_html'));

        // AJAX 핸들러 초기화
        new Donlinee_Waitlist_Ajax();

        // 관리자 페이지 초기화
        if (is_admin()) {
            new Donlinee_Waitlist_Admin();
        }
    }

    public function activate() {
        // 데이터베이스 테이블 생성
        Donlinee_Waitlist_Database::create_table();
    }

    public function deactivate() {
        // 필요한 정리 작업
    }

    public function init() {
        // 필요한 초기화 작업
    }

    public function enqueue_scripts() {
        // CSS 파일 로드
        wp_enqueue_style(
            'donlinee-waitlist',
            DONLINEE_WAITLIST_PLUGIN_URL . 'assets/css/waitlist.css',
            array(),
            DONLINEE_WAITLIST_VERSION
        );

        // JavaScript 파일 로드
        wp_enqueue_script(
            'donlinee-waitlist',
            DONLINEE_WAITLIST_PLUGIN_URL . 'assets/js/waitlist.js',
            array('jquery'),
            DONLINEE_WAITLIST_VERSION,
            true
        );

        // AJAX URL 및 nonce 전달
        wp_localize_script('donlinee-waitlist', 'donlinee_waitlist_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('donlinee-waitlist-nonce')
        ));
    }

    public function add_popup_html() {
        ?>
        <!-- 수강 대기 신청 팝업 -->
        <div id="donlinee-waitlist-popup" class="donlinee-popup-overlay" style="display: none;">
            <div class="donlinee-popup-container">
                <div class="donlinee-popup-header">
                    <h2>돈마고치 1기 수강 대기 신청</h2>
                    <button type="button" class="donlinee-popup-close">&times;</button>
                </div>

                <form id="donlinee-waitlist-form" class="donlinee-popup-form">
                    <div class="donlinee-form-group">
                        <label for="waitlist-name">성함 <span class="required">*</span></label>
                        <input type="text" id="waitlist-name" name="name" required placeholder="이름을 입력해주세요">
                        <span class="error-message" id="name-error"></span>
                    </div>

                    <div class="donlinee-form-group">
                        <label for="waitlist-phone">연락처 <span class="required">*</span></label>
                        <input type="tel" id="waitlist-phone" name="phone" required placeholder="010-0000-0000">
                        <span class="error-message" id="phone-error"></span>
                    </div>

                    <div class="donlinee-form-info">
                        <p>* 입력하신 연락처로 모집 시작일에 알림톡(카카오톡)이 발송됩니다.</p>
                        <p>* 기수별 오프라인 20명 한정, 50명 이상 지원 시 선착순 자동 마감</p>
                    </div>

                    <div class="donlinee-form-actions">
                        <button type="submit" class="donlinee-submit-btn">신청하기</button>
                    </div>
                </form>

                <!-- 성공 메시지 -->
                <div id="donlinee-success-message" style="display: none;">
                    <div class="success-icon">✓</div>
                    <h3>신청이 완료되었습니다!</h3>
                    <div class="success-content">
                        <p>안녕하세요 <span id="success-name"></span>님</p>
                        <p>돈마고치 1기 수강 대기 신청이 완료되었습니다.</p>
                        <p>기수별로 오프라인 20명으로 한정되며, <br/>서류 검토 기간상 50명 이상 지원 시 선착순 자동 마감됩니다.</p>
                        <p><span id="success-phone"></span>로 오픈 알림 메시지가 발송되오니 확인해주세요.</p>
                    </div>
                    <button type="button" class="donlinee-confirm-btn">확인했습니다</button>
                </div>
            </div>
        </div>
        <?php
    }
}

// 플러그인 초기화
Donlinee_Waitlist::get_instance();