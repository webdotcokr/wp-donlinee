<?php
/**
 * AJAX 핸들러 클래스
 */

if (!defined('ABSPATH')) {
    exit;
}

class Donlinee_Enrollment_Ajax {

    public function __construct() {
        // AJAX 액션 등록
        add_action('wp_ajax_donlinee_submit_enrollment', array($this, 'handle_enrollment_submission'));
        add_action('wp_ajax_nopriv_donlinee_submit_enrollment', array($this, 'handle_enrollment_submission'));

        add_action('wp_ajax_donlinee_update_payment_method', array($this, 'handle_payment_method'));
        add_action('wp_ajax_nopriv_donlinee_update_payment_method', array($this, 'handle_payment_method'));

        add_action('wp_ajax_donlinee_get_enrollment_details', array($this, 'get_enrollment_details'));

        // 팝업 HTML 동적 로딩용 AJAX 액션 추가
        add_action('wp_ajax_donlinee_load_enrollment_popup', array($this, 'ajax_load_enrollment_popup'));
        add_action('wp_ajax_nopriv_donlinee_load_enrollment_popup', array($this, 'ajax_load_enrollment_popup'));

        // CSV 내보내기 (관리자 전용)
        add_action('wp_ajax_donlinee_export_enrollments', array($this, 'handle_export_enrollments'));
    }

    /**
     * 수강 신청 처리
     */
    public function handle_enrollment_submission() {
        // nonce 확인
        check_ajax_referer('donlinee-enrollment-nonce', 'nonce');

        // 필수 필드 검증
        $required_fields = array('name', 'age_gender', 'phone', 'self_intro', 'sales_experience', 'application_reason', 'refund_account');

        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                wp_send_json_error(array(
                    'message' => '필수 항목을 모두 입력해주세요.',
                    'field' => $field
                ));
                return;
            }
        }

        // 전화번호 형식 검증 제거 - 어떤 형식이든 허용
        $phone = sanitize_text_field($_POST['phone']);
        // 형식 검증 제거 - 어떤 번호든 허용

        // 현재 설정 가져오기
        $settings = Donlinee_Enrollment_Settings::get_current_settings();

        // 모집 상태 확인
        if (!$settings['is_active']) {
            wp_send_json_error(array(
                'message' => '현재 모집이 마감되었습니다.'
            ));
            return;
        }

        // 최대 인원 확인 제거 - 20명 넘어도 계속 접수 받음
        // $current_count = Donlinee_Enrollment_Database::get_total_count('', $settings['batch_number']);
        // if ($current_count >= intval($settings['max_capacity'])) {
        //     wp_send_json_error(array(
        //         'message' => '모집 정원이 초과되어 신청이 마감되었습니다.'
        //     ));
        //     return;
        // }

        // 데이터 준비
        $enrollment_data = array(
            'name' => $_POST['name'],
            'age_gender' => $_POST['age_gender'],
            'phone' => $phone,
            'self_intro' => $_POST['self_intro'],
            'sales_experience' => $_POST['sales_experience'],
            'application_reason' => $_POST['application_reason'],
            'future_plans' => !empty($_POST['future_plans']) ? $_POST['future_plans'] : '',
            'refund_account' => $_POST['refund_account'],
            'batch_number' => $settings['batch_number']
        );

        // 데이터베이스에 저장
        $result = Donlinee_Enrollment_Database::insert_enrollment($enrollment_data);

        if ($result['success']) {
            // Slack 알림 발송
            $this->send_slack_notification($enrollment_data);

            // 최대 인원 도달 확인 제거 - 자동 마감하지 않음
            // Donlinee_Enrollment_Settings::check_capacity();

            wp_send_json_success(array(
                'message' => '수강 신청이 접수되었습니다.',
                'id' => $result['id'],
                'name' => $enrollment_data['name']
            ));
        } else {
            wp_send_json_error(array(
                'message' => $result['message']
            ));
        }
    }

    /**
     * 결제 방법 업데이트
     */
    public function handle_payment_method() {
        check_ajax_referer('donlinee-enrollment-nonce', 'nonce');

        $id = intval($_POST['id']);
        $payment_method = sanitize_text_field($_POST['payment_method']);

        if (!in_array($payment_method, array('transfer', 'card'))) {
            wp_send_json_error(array(
                'message' => '올바른 결제 방법이 아닙니다.'
            ));
            return;
        }

        $result = Donlinee_Enrollment_Database::update_payment_method($id, $payment_method);

        if ($result) {
            // 결제 방법 선택 알림
            $enrollment = Donlinee_Enrollment_Database::get_enrollment_by_id($id);
            $this->send_payment_notification($enrollment, $payment_method);

            wp_send_json_success(array(
                'message' => '결제 방법이 선택되었습니다.',
                'payment_method' => $payment_method
            ));
        } else {
            wp_send_json_error(array(
                'message' => '결제 방법 선택 중 오류가 발생했습니다.'
            ));
        }
    }

    /**
     * 신청 상세 정보 가져오기 (관리자용)
     */
    public function get_enrollment_details() {
        check_ajax_referer('donlinee-enrollment-admin-nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('권한이 없습니다.');
            return;
        }

        $id = intval($_POST['id']);
        $enrollment = Donlinee_Enrollment_Database::get_enrollment_by_id($id);

        if ($enrollment) {
            ob_start();
            ?>
            <table class="widefat">
                <tr>
                    <th width="30%">항목</th>
                    <th>내용</th>
                </tr>
                <tr>
                    <td><strong>이름</strong></td>
                    <td><?php echo esc_html($enrollment['name']); ?></td>
                </tr>
                <tr>
                    <td><strong>나이/성별</strong></td>
                    <td><?php echo esc_html($enrollment['age_gender']); ?></td>
                </tr>
                <tr>
                    <td><strong>연락처</strong></td>
                    <td><?php echo esc_html($enrollment['phone']); ?></td>
                </tr>
                <tr>
                    <td><strong>자기소개</strong></td>
                    <td><?php echo nl2br(esc_html($enrollment['self_intro'])); ?></td>
                </tr>
                <tr>
                    <td><strong>판매 경험</strong></td>
                    <td><?php echo nl2br(esc_html($enrollment['sales_experience'])); ?></td>
                </tr>
                <tr>
                    <td><strong>지원 이유</strong></td>
                    <td><?php echo nl2br(esc_html($enrollment['application_reason'])); ?></td>
                </tr>
                <tr>
                    <td><strong>향후 계획</strong></td>
                    <td><?php echo nl2br(esc_html($enrollment['future_plans'] ?: '미작성')); ?></td>
                </tr>
                <tr>
                    <td><strong>환불 계좌</strong></td>
                    <td><?php echo esc_html($enrollment['refund_account']); ?></td>
                </tr>
                <tr>
                    <td><strong>결제 방법</strong></td>
                    <td>
                        <?php
                        if ($enrollment['payment_method'] === 'transfer') {
                            echo '계좌이체';
                        } elseif ($enrollment['payment_method'] === 'card') {
                            echo '카드결제';
                        } else {
                            echo '미선택';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>결제 상태</strong></td>
                    <td>
                        <?php
                        $status_labels = array(
                            'submitted' => '신청완료',
                            'payment_pending' => '결제대기',
                            'paid' => '결제완료',
                            'cancelled' => '취소'
                        );
                        echo $status_labels[$enrollment['payment_status']] ?? $enrollment['payment_status'];
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>신청일시</strong></td>
                    <td><?php echo esc_html($enrollment['created_at']); ?></td>
                </tr>
            </table>
            <?php
            $html = ob_get_clean();

            wp_send_json_success(array(
                'html' => $html
            ));
        } else {
            wp_send_json_error('신청 정보를 찾을 수 없습니다.');
        }
    }

    /**
     * Slack 알림 발송 (신청 접수)
     */
    private function send_slack_notification($data) {
        if (!defined('SLACK_WEBHOOK_URL') || !SLACK_WEBHOOK_URL) {
            return;
        }

        $settings = Donlinee_Enrollment_Settings::get_current_settings();

        $message = array(
            'text' => sprintf(
                "🎓 *돈마고치 %d기 수강 신청*\n이름: %s\n나이/성별: %s\n연락처: %s\n자기소개: %s\n판매경험: %s\n지원이유: %s",
                $settings['batch_number'],
                $data['name'],
                $data['age_gender'],
                $data['phone'],
                mb_substr($data['self_intro'], 0, 100),
                mb_substr($data['sales_experience'], 0, 100),
                mb_substr($data['application_reason'], 0, 100)
            ),
            'channel' => defined('SLACK_CHANNEL') ? SLACK_CHANNEL : null
        );

        wp_remote_post(SLACK_WEBHOOK_URL, array(
            'body' => json_encode($message),
            'headers' => array('Content-Type' => 'application/json')
        ));
    }

    /**
     * Slack 알림 발송 (결제 방법 선택)
     */
    private function send_payment_notification($enrollment, $payment_method) {
        if (!defined('SLACK_WEBHOOK_URL') || !SLACK_WEBHOOK_URL) {
            return;
        }

        $method_text = $payment_method === 'transfer' ? '계좌이체' : '카드결제';

        $message = array(
            'text' => sprintf(
                "💳 *결제 방법 선택*\n이름: %s\n연락처: %s\n결제방법: %s",
                $enrollment['name'],
                $enrollment['phone'],
                $method_text
            ),
            'channel' => defined('SLACK_CHANNEL') ? SLACK_CHANNEL : null
        );

        wp_remote_post(SLACK_WEBHOOK_URL, array(
            'body' => json_encode($message),
            'headers' => array('Content-Type' => 'application/json')
        ));
    }

    /**
     * CSV 내보내기 처리
     */
    public function handle_export_enrollments() {
        check_ajax_referer('donlinee-enrollment-admin-nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('권한이 없습니다.');
        }

        $settings = Donlinee_Enrollment_Settings::get_current_settings();
        $batch_number = isset($_GET['batch']) ? intval($_GET['batch']) : $settings['batch_number'];

        $export_data = Donlinee_Enrollment_Database::get_export_data($batch_number);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="enrollments_' . $batch_number . '기_' . date('Y-m-d') . '.csv"');

        echo "\xEF\xBB\xBF";

        $output = fopen('php://output', 'w');
        foreach ($export_data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);

        exit;
    }

    /**
     * AJAX로 팝업 HTML 로드
     */
    public function ajax_load_enrollment_popup() {
        check_ajax_referer('donlinee-enrollment-nonce', 'nonce');

        $settings = Donlinee_Enrollment_Settings::get_current_settings();

        if ($settings['mode'] !== 'enrollment' || !$settings['is_active']) {
            wp_send_json_error('Enrollment is not active');
            return;
        }

        // HTML을 버퍼에 캡처
        ob_start();
        Donlinee_Enrollment_Forms::render_enrollment_popup();
        $html = ob_get_clean();

        wp_send_json_success(array(
            'html' => $html,
            'batch_number' => $settings['batch_number']
        ));
    }
}