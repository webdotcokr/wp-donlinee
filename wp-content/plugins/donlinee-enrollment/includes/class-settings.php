<?php
/**
 * 설정 관리 클래스
 */

if (!defined('ABSPATH')) {
    exit;
}

class Donlinee_Enrollment_Settings {

    private static $option_name = 'donlinee_enrollment_settings';

    /**
     * 기본 설정 생성
     */
    public static function create_default_settings() {
        if (get_option(self::$option_name)) {
            return;
        }

        $defaults = array(
            'mode' => 'waitlist', // waitlist or enrollment
            'batch_number' => 1,
            'start_date' => current_time('Y-m-d\TH:i'),
            'end_date' => date('Y-m-d\TH:i', strtotime('+7 days')),
            'auto_switch_date' => date('Y-m-d\TH:i', strtotime('+3 days')),
            'max_capacity' => 20,
            'is_active' => true,
            // 텍스트 설정 기본값
            'waitlist_button_text' => '수강 대기신청',
            'enrollment_button_text' => '(OPEN) 수강 신청하기',
            'countdown_text_waitlist' => '모집 시작까지',
            'countdown_text_enrollment' => '모집 마감까지'
        );

        add_option(self::$option_name, $defaults);
    }

    /**
     * 현재 설정 가져오기
     */
    public static function get_current_settings() {
        $settings = get_option(self::$option_name);
        
        // 설정이 없거나 깨졌을 경우 기본값 반환 (안전장치)
        if (!$settings || !is_array($settings)) {
            return array(
                'mode' => 'waitlist',
                'batch_number' => 1,
                'start_date' => current_time('Y-m-d\TH:i'),
                'end_date' => date('Y-m-d\TH:i', strtotime('+7 days')),
                'auto_switch_date' => date('Y-m-d\TH:i', strtotime('+3 days')),
                'max_capacity' => 20,
                'is_active' => true,
                'waitlist_button_text' => '수강 대기신청',
                'enrollment_button_text' => '(OPEN) 수강 신청하기',
                'countdown_text_waitlist' => '모집 시작까지',
                'countdown_text_enrollment' => '모집 마감까지'
            );
        }
        
        return $settings;
    }

    /**
     * 특정 설정 값 가져오기
     */
    public static function get_setting($key) {
        $settings = self::get_current_settings();
        return isset($settings[$key]) ? $settings[$key] : null;
    }

    /**
     * 설정 전체 업데이트
     */
    public static function update_settings($new_settings) {
        $current = self::get_current_settings();
        $merged = array_merge($current, $new_settings);
        return update_option(self::$option_name, $merged);
    }

    /**
     * 개별 설정 업데이트
     */
    public static function update_setting($key, $value) {
        $settings = self::get_current_settings();
        $settings[$key] = $value;
        return update_option(self::$option_name, $settings);
    }

    /**
     * 모드 변경 (가장 중요한 함수)
     */
    public static function update_mode($mode) {
        if (!in_array($mode, array('waitlist', 'enrollment'))) {
            return false;
        }

        $settings = self::get_current_settings();
        $settings['mode'] = $mode;
        
        // 모드가 바뀌면 is_active 상태도 지능적으로 처리
        if ($mode === 'enrollment') {
            $settings['is_active'] = true;
        }
        
        return update_option(self::$option_name, $settings);
    }
}