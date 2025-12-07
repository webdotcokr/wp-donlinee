<?php
/**
 * AJAX 핸들러 클래스
 */

if (!defined('ABSPATH')) {
    exit;
}

class Donlinee_Waitlist_Ajax {

    public function __construct() {
        // AJAX 액션 등록
        add_action('wp_ajax_donlinee_waitlist_submit', array($this, 'handle_waitlist_submission'));
        add_action('wp_ajax_nopriv_donlinee_waitlist_submit', array($this, 'handle_waitlist_submission'));

        // 관리자 AJAX 액션
        add_action('wp_ajax_donlinee_waitlist_update_status', array($this, 'handle_status_update'));
        add_action('wp_ajax_donlinee_waitlist_delete', array($this, 'handle_delete'));
        add_action('wp_ajax_donlinee_waitlist_export', array($this, 'handle_export'));
    }

    /**
     * 대기 신청 폼 제출 처리
     */
    public function handle_waitlist_submission() {
        // nonce 검증
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'donlinee-waitlist-nonce')) {
            wp_send_json_error(array('message' => '보안 검증에 실패했습니다.'));
        }

        // 데이터 검증
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';

        if (empty($name) || empty($phone)) {
            wp_send_json_error(array('message' => '필수 정보를 모두 입력해주세요.'));
        }

        // 이름 길이 검증
        if (mb_strlen($name, 'UTF-8') < 2) {
            wp_send_json_error(array('message' => '이름을 2자 이상 입력해주세요.'));
        }

        // 전화번호 형식 검증
        if (!preg_match('/^01[0-9]-[0-9]{3,4}-[0-9]{4}$/', $phone)) {
            wp_send_json_error(array('message' => '올바른 전화번호 형식을 입력해주세요.'));
        }

        // 데이터베이스에 저장
        $result = Donlinee_Waitlist_Database::insert_application(array(
            'name' => $name,
            'phone' => $phone
        ));

        if ($result['success']) {
            // 관리자 이메일 알림 (옵션)
            $this->send_admin_notification($name, $phone);

            // 성공 응답
            wp_send_json_success(array(
                'message' => '신청이 완료되었습니다.',
                'id' => $result['id']
            ));
        } else {
            wp_send_json_error(array('message' => $result['message']));
        }
    }

    /**
     * 관리자에게 이메일 알림 전송
     */
    private function send_admin_notification($name, $phone) {
        $admin_email = get_option('admin_email');
        $site_name = get_bloginfo('name');

        $subject = "[{$site_name}] 새로운 수강 대기 신청";

        $message = "안녕하세요,\n\n";
        $message .= "돈마고치 1기 수강 대기 신청이 접수되었습니다.\n\n";
        $message .= "신청자 정보:\n";
        $message .= "- 이름: {$name}\n";
        $message .= "- 연락처: {$phone}\n";
        $message .= "- 신청일시: " . current_time('mysql') . "\n\n";
        $message .= "관리자 페이지에서 신청 목록을 확인하실 수 있습니다.\n";
        $message .= admin_url('admin.php?page=donlinee-waitlist');

        $headers = array(
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . $site_name . ' <' . $admin_email . '>'
        );

        // 이메일 전송 (실패해도 신청은 계속 처리)
        @wp_mail($admin_email, $subject, $message, $headers);
    }

    /**
     * 상태 업데이트 처리 (관리자)
     */
    public function handle_status_update() {
        // 권한 확인
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => '권한이 없습니다.'));
        }

        // nonce 검증
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'donlinee-waitlist-admin-nonce')) {
            wp_send_json_error(array('message' => '보안 검증에 실패했습니다.'));
        }

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';

        if (!$id || !in_array($status, array('pending', 'confirmed', 'cancelled'))) {
            wp_send_json_error(array('message' => '잘못된 요청입니다.'));
        }

        $result = Donlinee_Waitlist_Database::update_status($id, $status);

        if ($result !== false) {
            wp_send_json_success(array('message' => '상태가 업데이트되었습니다.'));
        } else {
            wp_send_json_error(array('message' => '업데이트에 실패했습니다.'));
        }
    }

    /**
     * 신청 삭제 처리 (관리자)
     */
    public function handle_delete() {
        // 권한 확인
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => '권한이 없습니다.'));
        }

        // nonce 검증
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'donlinee-waitlist-admin-nonce')) {
            wp_send_json_error(array('message' => '보안 검증에 실패했습니다.'));
        }

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if (!$id) {
            wp_send_json_error(array('message' => '잘못된 요청입니다.'));
        }

        $result = Donlinee_Waitlist_Database::delete_application($id);

        if ($result !== false) {
            wp_send_json_success(array('message' => '삭제되었습니다.'));
        } else {
            wp_send_json_error(array('message' => '삭제에 실패했습니다.'));
        }
    }

    /**
     * CSV 내보내기 처리 (관리자)
     */
    public function handle_export() {
        // 권한 확인
        if (!current_user_can('manage_options')) {
            wp_die('권한이 없습니다.');
        }

        // nonce 검증
        if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'donlinee-waitlist-export-nonce')) {
            wp_die('보안 검증에 실패했습니다.');
        }

        // CSV 데이터 가져오기
        $data = Donlinee_Waitlist_Database::get_export_data();

        // CSV 헤더 설정
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="donlinee-waitlist-' . date('Y-m-d-His') . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // BOM 추가 (엑셀에서 한글 깨짐 방지)
        echo "\xEF\xBB\xBF";

        // CSV 출력
        $output = fopen('php://output', 'w');
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);

        exit;
    }
}