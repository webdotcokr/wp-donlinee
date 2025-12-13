<?php
/**
 * 설정 관리 클래스
 */

if (!defined('ABSPATH')) {
    exit;
}

class Donlinee_Enrollment_Settings {

    /**
     * 기본 설정 생성
     */
    public static function create_default_settings() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'donlinee_enrollment_settings';

        // 기본 설정값
        $default_settings = array(
            'mode' => 'waitlist', // 기본은 대기신청 모드
            'batch_number' => '1',
            'start_date' => '2025-12-13 11:00:00',
            'end_date' => '2025-12-28 23:59:59',
            'auto_switch_date' => '2025-12-13 11:00:00',
            'max_capacity' => '20',
            'is_active' => 'true',
            // 버튼 텍스트 설정
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
    }

    /**
     * 현재 설정 가져오기
     */
    public static function get_current_settings() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'donlinee_enrollment_settings';

        $results = $wpdb->get_results(
            "SELECT setting_key, setting_value FROM $table_name",
            ARRAY_A
        );

        $settings = array();
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        // 불린 값 변환
        $settings['is_active'] = ($settings['is_active'] === 'true');

        return $settings;
    }

    /**
     * 특정 설정값 가져오기
     */
    public static function get_setting($key) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'donlinee_enrollment_settings';

        return $wpdb->get_var(
            $wpdb->prepare("SELECT setting_value FROM $table_name WHERE setting_key = %s", $key)
        );
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
            return $wpdb->update(
                $table_name,
                array('setting_value' => $value, 'updated_at' => current_time('mysql')),
                array('setting_key' => $key),
                array('%s', '%s'),
                array('%s')
            );
        } else {
            return $wpdb->insert(
                $table_name,
                array(
                    'setting_key' => $key,
                    'setting_value' => $value,
                    'created_at' => current_time('mysql')
                ),
                array('%s', '%s', '%s')
            );
        }
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

        foreach ($settings as $key => $value) {
            if (!self::update_setting($key, $value)) {
                $success = false;
            }
        }

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
        return self::get_setting('is_active') === 'true';
    }

    /**
     * 현재 기수 가져오기
     */
    public static function get_current_batch() {
        return intval(self::get_setting('batch_number'));
    }

    /**
     * 최대 인원 확인 (자동 마감 제거 - 20명 넘어도 계속 접수)
     */
    public static function check_capacity() {
        $max_capacity = intval(self::get_setting('max_capacity'));
        $batch_number = self::get_current_batch();

        $current_count = Donlinee_Enrollment_Database::get_total_count('', $batch_number);

        // 자동 마감하지 않음 - 20명 넘어도 계속 접수 받고 나중에 선발
        // if ($current_count >= $max_capacity) {
        //     self::update_setting('is_active', 'false');
        //     return true; // 마감됨
        // }

        return false; // 항상 여유 있음으로 리턴
    }
}