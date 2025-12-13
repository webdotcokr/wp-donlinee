<?php
/**
 * 최적화된 설정 관리 클래스 - 캐싱 구현
 *
 * 이 파일은 기존 class-settings.php를 대체합니다.
 * 트랜지언트 API를 사용하여 데이터베이스 쿼리를 캐싱합니다.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Donlinee_Enrollment_Settings {

    // 캐시 키 상수
    const CACHE_KEY = 'donlinee_enrollment_settings_cache';
    const CACHE_EXPIRY = 300; // 5분 캐싱

    /**
     * 기본 설정 생성
     */
    public static function create_default_settings() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'donlinee_enrollment_settings';

        // 기본 설정값
        $default_settings = array(
            'mode' => 'waitlist',
            'batch_number' => '1',
            'start_date' => '2025-12-13 11:00:00',
            'end_date' => '2025-12-28 23:59:59',
            'auto_switch_date' => '2025-12-13 11:00:00',
            'max_capacity' => '20',
            'is_active' => 'true',
            'waitlist_button_text' => '수강 대기신청',
            'enrollment_button_text' => '(OPEN) 수강 신청하기',
            'countdown_text_waitlist' => '모집 시작까지',
            'countdown_text_enrollment' => '모집 마감까지'
        );

        foreach ($default_settings as $key => $value) {
            $existing = $wpdb->get_var(
                $wpdb->prepare("SELECT id FROM $table_name WHERE setting_key = %s", $key)
            );

            if (!$existing) {
                $wpdb->insert(
                    $table_name,
                    array(
                        'setting_key' => $key,
                        'setting_value' => $value
                    ),
                    array('%s', '%s')
                );
            }
        }

        // 캐시 클리어
        self::clear_cache();
    }

    /**
     * 현재 설정 가져오기 - 캐싱 구현
     */
    public static function get_current_settings() {
        // 메모리 캐시 확인 (같은 요청 내에서 중복 쿼리 방지)
        static $memory_cache = null;
        if ($memory_cache !== null) {
            return $memory_cache;
        }

        // WordPress 트랜지언트 캐시 확인
        $cached_settings = get_transient(self::CACHE_KEY);
        if ($cached_settings !== false) {
            $memory_cache = $cached_settings;
            return $cached_settings;
        }

        // 캐시가 없으면 DB에서 가져오기
        global $wpdb;
        $table_name = $wpdb->prefix . 'donlinee_enrollment_settings';

        // 최적화된 단일 쿼리로 모든 설정 가져오기
        $results = $wpdb->get_results(
            "SELECT setting_key, setting_value FROM $table_name",
            ARRAY_A
        );

        if (!$results) {
            // 설정이 없으면 기본값 생성
            self::create_default_settings();
            $results = $wpdb->get_results(
                "SELECT setting_key, setting_value FROM $table_name",
                ARRAY_A
            );
        }

        $settings = array();
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        // 불린 값 변환
        $settings['is_active'] = ($settings['is_active'] === 'true');

        // 캐시에 저장
        set_transient(self::CACHE_KEY, $settings, self::CACHE_EXPIRY);
        $memory_cache = $settings;

        return $settings;
    }

    /**
     * 특정 설정값 가져오기 - 캐싱 활용
     */
    public static function get_setting($key) {
        $settings = self::get_current_settings();
        return isset($settings[$key]) ? $settings[$key] : null;
    }

    /**
     * 설정 업데이트
     */
    public static function update_setting($key, $value) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'donlinee_enrollment_settings';

        // 불린 값 처리
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }

        $existing = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM $table_name WHERE setting_key = %s", $key)
        );

        if ($existing) {
            $result = $wpdb->update(
                $table_name,
                array('setting_value' => $value, 'updated_at' => current_time('mysql')),
                array('setting_key' => $key),
                array('%s', '%s'),
                array('%s')
            );
        } else {
            $result = $wpdb->insert(
                $table_name,
                array(
                    'setting_key' => $key,
                    'setting_value' => $value,
                    'created_at' => current_time('mysql')
                ),
                array('%s', '%s', '%s')
            );
        }

        // 캐시 클리어
        self::clear_cache();

        return $result;
    }

    /**
     * 모드 업데이트
     */
    public static function update_mode($mode) {
        $valid_modes = array('waitlist', 'enrollment');

        if (!in_array($mode, $valid_modes)) {
            return false;
        }

        return self::update_setting('mode', $mode);
    }

    /**
     * 여러 설정 한번에 업데이트
     */
    public static function update_settings($settings) {
        $success = true;

        // 트랜잭션 시작을 위해 autocommit 비활성화
        global $wpdb;
        $wpdb->query('START TRANSACTION');

        foreach ($settings as $key => $value) {
            if (!self::update_setting($key, $value)) {
                $success = false;
                break;
            }
        }

        if ($success) {
            $wpdb->query('COMMIT');
        } else {
            $wpdb->query('ROLLBACK');
        }

        // 캐시 클리어
        self::clear_cache();

        return $success;
    }

    /**
     * 현재 모드가 수강신청 모드인지 확인
     */
    public static function is_enrollment_mode() {
        return self::get_setting('mode') === 'enrollment';
    }

    /**
     * 현재 모드가 활성화 상태인지 확인
     */
    public static function is_active() {
        $settings = self::get_current_settings();
        return $settings['is_active'];
    }

    /**
     * 현재 기수 가져오기
     */
    public static function get_current_batch() {
        return intval(self::get_setting('batch_number'));
    }

    /**
     * 최대 인원 확인
     */
    public static function check_capacity() {
        $max_capacity = intval(self::get_setting('max_capacity'));
        $batch_number = self::get_current_batch();

        // 카운트도 캐싱 (1분간)
        $count_cache_key = 'donlinee_enrollment_count_' . $batch_number;
        $current_count = get_transient($count_cache_key);

        if ($current_count === false) {
            $current_count = Donlinee_Enrollment_Database::get_total_count('', $batch_number);
            set_transient($count_cache_key, $current_count, 60); // 1분 캐싱
        }

        // 20명 넘어도 계속 접수 받기 (자동 마감 안 함)
        return false;
    }

    /**
     * 캐시 클리어
     */
    public static function clear_cache() {
        delete_transient(self::CACHE_KEY);

        // 모든 카운트 캐시도 클리어
        global $wpdb;
        $wpdb->query(
            "DELETE FROM {$wpdb->options}
             WHERE option_name LIKE '_transient_donlinee_enrollment_count_%'
             OR option_name LIKE '_transient_timeout_donlinee_enrollment_count_%'"
        );
    }

    /**
     * 설정을 AJAX로 비동기 로드하기 위한 메소드
     * 페이지 로드 성능 개선용
     */
    public static function get_ajax_settings() {
        // 필수 설정만 반환 (타이머용)
        $settings = self::get_current_settings();
        return array(
            'mode' => $settings['mode'],
            'start_date' => $settings['start_date'],
            'end_date' => $settings['end_date'],
            'is_active' => $settings['is_active']
        );
    }
}