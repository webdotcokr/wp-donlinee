<?php
/**
 * 데이터베이스 관련 클래스
 */

if (!defined('ABSPATH')) {
    exit;
}

class Donlinee_Waitlist_Database {

    /**
     * 테이블 이름 가져오기
     */
    public static function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'donlinee_waitlist';
    }

    /**
     * 데이터베이스 테이블 생성
     */
    public static function create_table() {
        global $wpdb;

        $table_name = self::get_table_name();
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            phone varchar(20) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            status varchar(20) DEFAULT 'pending',
            notes text,
            PRIMARY KEY (id),
            KEY phone_index (phone),
            KEY status_index (status),
            KEY created_at_index (created_at)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * 신청 데이터 저장
     */
    public static function insert_application($data) {
        global $wpdb;

        $table_name = self::get_table_name();

        // 중복 체크 (같은 번호로 이미 신청했는지)
        $existing = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE phone = %s",
                $data['phone']
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
                'phone' => sanitize_text_field($data['phone']),
                'created_at' => current_time('mysql'),
                'status' => 'pending'
            ),
            array('%s', '%s', '%s', '%s')
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
     * 모든 신청 목록 가져오기
     */
    public static function get_all_applications($args = array()) {
        global $wpdb;

        $defaults = array(
            'orderby' => 'created_at',
            'order' => 'DESC',
            'limit' => -1,
            'offset' => 0,
            'status' => ''
        );

        $args = wp_parse_args($args, $defaults);
        $table_name = self::get_table_name();

        $query = "SELECT * FROM $table_name";
        $where = array();

        if (!empty($args['status'])) {
            $where[] = $wpdb->prepare("status = %s", $args['status']);
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
    public static function update_status($id, $status) {
        global $wpdb;

        $table_name = self::get_table_name();

        return $wpdb->update(
            $table_name,
            array('status' => $status),
            array('id' => $id),
            array('%s'),
            array('%d')
        );
    }

    /**
     * 신청 삭제
     */
    public static function delete_application($id) {
        global $wpdb;

        $table_name = self::get_table_name();

        return $wpdb->delete(
            $table_name,
            array('id' => $id),
            array('%d')
        );
    }

    /**
     * 전체 신청 수 가져오기
     */
    public static function get_total_count($status = '') {
        global $wpdb;

        $table_name = self::get_table_name();

        if (!empty($status)) {
            return $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE status = %s",
                    $status
                )
            );
        }

        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    }

    /**
     * CSV 내보내기용 데이터 가져오기
     */
    public static function get_export_data() {
        $applications = self::get_all_applications();

        $export_data = array();
        $export_data[] = array('ID', '이름', '연락처', '신청일시', '상태');

        foreach ($applications as $app) {
            $export_data[] = array(
                $app['id'],
                $app['name'],
                $app['phone'],
                $app['created_at'],
                $app['status']
            );
        }

        return $export_data;
    }
}