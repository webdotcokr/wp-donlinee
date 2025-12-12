<?php
/**
 * 데이터베이스 관련 클래스
 */

if (!defined('ABSPATH')) {
    exit;
}

class Donlinee_Enrollment_Database {

    /**
     * 수강 신청 테이블 이름 가져오기
     */
    public static function get_enrollments_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'donlinee_enrollments';
    }

    /**
     * 설정 테이블 이름 가져오기
     */
    public static function get_settings_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'donlinee_enrollment_settings';
    }

    /**
     * 데이터베이스 테이블 생성
     */
    public static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // 수강 신청 테이블
        $enrollments_table = self::get_enrollments_table_name();
        $sql_enrollments = "CREATE TABLE IF NOT EXISTS $enrollments_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            age_gender varchar(50) NOT NULL,
            phone varchar(20) NOT NULL,
            self_intro text NOT NULL,
            sales_experience text NOT NULL,
            application_reason text NOT NULL,
            future_plans text,
            refund_account varchar(200) NOT NULL,
            payment_method varchar(20),
            payment_status varchar(20) DEFAULT 'submitted',
            batch_number int(11) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY phone_index (phone),
            KEY payment_status_index (payment_status),
            KEY batch_index (batch_number),
            KEY created_at_index (created_at)
        ) $charset_collate;";

        // 설정 테이블
        $settings_table = self::get_settings_table_name();
        $sql_settings = "CREATE TABLE IF NOT EXISTS $settings_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            setting_key varchar(100) NOT NULL,
            setting_value text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY setting_key_unique (setting_key)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_enrollments);
        dbDelta($sql_settings);
    }

    /**
     * 수강 신청 데이터 저장
     */
    public static function insert_enrollment($data) {
        global $wpdb;

        $table_name = self::get_enrollments_table_name();

        // 중복 체크 (같은 번호로 이미 신청했는지)
        $existing = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE phone = %s AND batch_number = %d",
                $data['phone'],
                $data['batch_number']
            )
        );

        if ($existing > 0) {
            return array(
                'success' => false,
                'message' => '이미 신청하신 연락처입니다.'
            );
        }

        $result = $wpdb->insert(
            $table_name,
            array(
                'name' => sanitize_text_field($data['name']),
                'age_gender' => sanitize_text_field($data['age_gender']),
                'phone' => sanitize_text_field($data['phone']),
                'self_intro' => sanitize_textarea_field($data['self_intro']),
                'sales_experience' => sanitize_textarea_field($data['sales_experience']),
                'application_reason' => sanitize_textarea_field($data['application_reason']),
                'future_plans' => !empty($data['future_plans']) ? sanitize_textarea_field($data['future_plans']) : null,
                'refund_account' => sanitize_text_field($data['refund_account']),
                'payment_method' => null,
                'payment_status' => 'submitted',
                'batch_number' => intval($data['batch_number']),
                'created_at' => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s')
        );

        if ($result === false) {
            return array(
                'success' => false,
                'message' => '신청 중 오류가 발생했습니다. 다시 시도해주세요.'
            );
        }

        return array(
            'success' => true,
            'id' => $wpdb->insert_id,
            'message' => '신청이 완료되었습니다.'
        );
    }

    /**
     * 결제 방법 업데이트
     */
    public static function update_payment_method($id, $payment_method) {
        global $wpdb;

        $table_name = self::get_enrollments_table_name();

        $result = $wpdb->update(
            $table_name,
            array(
                'payment_method' => $payment_method,
                'payment_status' => 'payment_pending',
                'updated_at' => current_time('mysql')
            ),
            array('id' => $id),
            array('%s', '%s', '%s'),
            array('%d')
        );

        return $result !== false;
    }

    /**
     * 모든 신청 목록 가져오기
     */
    public static function get_all_enrollments($args = array()) {
        global $wpdb;

        $defaults = array(
            'orderby' => 'created_at',
            'order' => 'DESC',
            'limit' => -1,
            'offset' => 0,
            'payment_status' => '',
            'batch_number' => 0
        );

        $args = wp_parse_args($args, $defaults);
        $table_name = self::get_enrollments_table_name();

        $query = "SELECT * FROM $table_name";
        $where = array();

        if (!empty($args['payment_status'])) {
            $where[] = $wpdb->prepare("payment_status = %s", $args['payment_status']);
        }

        if ($args['batch_number'] > 0) {
            $where[] = $wpdb->prepare("batch_number = %d", $args['batch_number']);
        }

        if (!empty($where)) {
            $query .= " WHERE " . implode(' AND ', $where);
        }

        $query .= " ORDER BY {$args['orderby']} {$args['order']}";

        if ($args['limit'] > 0) {
            $query .= $wpdb->prepare(" LIMIT %d OFFSET %d", $args['limit'], $args['offset']);
        }

        return $wpdb->get_results($query, ARRAY_A);
    }

    /**
     * 신청 상태 업데이트
     */
    public static function update_payment_status($id, $status) {
        global $wpdb;

        $table_name = self::get_enrollments_table_name();

        return $wpdb->update(
            $table_name,
            array('payment_status' => $status, 'updated_at' => current_time('mysql')),
            array('id' => $id),
            array('%s', '%s'),
            array('%d')
        );
    }

    /**
     * 신청 삭제
     */
    public static function delete_enrollment($id) {
        global $wpdb;

        $table_name = self::get_enrollments_table_name();

        return $wpdb->delete(
            $table_name,
            array('id' => $id),
            array('%d')
        );
    }

    /**
     * 전체 신청 수 가져오기
     */
    public static function get_total_count($payment_status = '', $batch_number = 0) {
        global $wpdb;

        $table_name = self::get_enrollments_table_name();
        $where = array('1=1');

        if (!empty($payment_status)) {
            $where[] = $wpdb->prepare("payment_status = %s", $payment_status);
        }

        if ($batch_number > 0) {
            $where[] = $wpdb->prepare("batch_number = %d", $batch_number);
        }

        $query = "SELECT COUNT(*) FROM $table_name WHERE " . implode(' AND ', $where);
        return $wpdb->get_var($query);
    }

    /**
     * CSV 내보내기용 데이터 가져오기
     */
    public static function get_export_data($batch_number = 0) {
        $enrollments = self::get_all_enrollments(array('batch_number' => $batch_number));

        $export_data = array();
        $export_data[] = array(
            'ID', '이름', '나이/성별', '연락처', '자기소개',
            '판매경험', '지원이유', '향후계획', '환불계좌',
            '결제방법', '결제상태', '기수', '신청일시'
        );

        foreach ($enrollments as $enrollment) {
            $export_data[] = array(
                $enrollment['id'],
                $enrollment['name'],
                $enrollment['age_gender'],
                $enrollment['phone'],
                $enrollment['self_intro'],
                $enrollment['sales_experience'],
                $enrollment['application_reason'],
                $enrollment['future_plans'],
                $enrollment['refund_account'],
                $enrollment['payment_method'],
                $enrollment['payment_status'],
                $enrollment['batch_number'],
                $enrollment['created_at']
            );
        }

        return $export_data;
    }

    /**
     * 특정 ID의 신청 정보 가져오기
     */
    public static function get_enrollment_by_id($id) {
        global $wpdb;

        $table_name = self::get_enrollments_table_name();

        return $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id),
            ARRAY_A
        );
    }
}