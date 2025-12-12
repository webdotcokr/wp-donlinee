<?php
/**
 * 관리자 페이지 클래스
 */

if (!defined('ABSPATH')) {
    exit;
}

class Donlinee_Enrollment_Admin {

    public function __construct() {
        // 관리자 메뉴 추가
        add_action('admin_menu', array($this, 'add_admin_menu'));

        // 관리자 스크립트 및 스타일 로드
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        // AJAX 핸들러
        add_action('wp_ajax_donlinee_save_settings', array($this, 'ajax_save_settings'));
        add_action('wp_ajax_donlinee_switch_mode', array($this, 'ajax_switch_mode'));
        add_action('wp_ajax_donlinee_export_enrollments', array($this, 'ajax_export_enrollments'));
        add_action('wp_ajax_donlinee_update_enrollment_status', array($this, 'ajax_update_enrollment_status'));
        add_action('wp_ajax_donlinee_delete_enrollment', array($this, 'ajax_delete_enrollment'));
    }

    /**
     * 관리자 메뉴 추가
     */
    public function add_admin_menu() {
        add_menu_page(
            '수강 신청 관리',
            '수강 신청 관리',
            'manage_options',
            'donlinee-enrollment',
            array($this, 'render_admin_page'),
            'dashicons-welcome-learn-more',
            31
        );

        // 설정 페이지만 남김 (서브메뉴 제거)
        // 실제 수강 신청자 목록은 모드가 enrollment일 때만 의미있음
        // 대기 신청자는 기존 플러그인에서 관리
    }

    /**
     * 관리자 스크립트 및 스타일 로드
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'donlinee-enrollment') === false) {
            return;
        }

        // 관리자 CSS
        wp_enqueue_style(
            'donlinee-enrollment-admin',
            DONLINEE_ENROLLMENT_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            DONLINEE_ENROLLMENT_VERSION
        );

        // 관리자 JavaScript
        wp_enqueue_script(
            'donlinee-enrollment-admin',
            DONLINEE_ENROLLMENT_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            DONLINEE_ENROLLMENT_VERSION,
            true
        );

        // AJAX 설정
        wp_localize_script('donlinee-enrollment-admin', 'donlinee_enrollment_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('donlinee-enrollment-admin-nonce')
        ));
    }

    /**
     * 설정 페이지 렌더링
     */
    public function render_admin_page() {
        $settings = Donlinee_Enrollment_Settings::get_current_settings();
        $current_batch_count = Donlinee_Enrollment_Database::get_total_count('', $settings['batch_number']);
        ?>
        <div class="wrap">
            <h1>수강 신청 관리 시스템</h1>

            <!-- 현재 상태 대시보드 -->
            <div class="donlinee-status-dashboard">
                <div class="status-card <?php echo $settings['mode'] === 'enrollment' ? 'active' : ''; ?>">
                    <h3>현재 모드</h3>
                    <p class="status-value">
                        <?php echo $settings['mode'] === 'enrollment' ? '🟢 수강 신청' : '🟡 대기 신청'; ?>
                    </p>
                    <button class="button button-primary" id="quick-switch-mode">
                        <?php echo $settings['mode'] === 'enrollment' ? '대기 신청으로 전환' : '수강 신청으로 전환'; ?>
                    </button>
                </div>

                <div class="status-card">
                    <h3>현재 기수</h3>
                    <p class="status-value"><?php echo $settings['batch_number']; ?>기</p>
                </div>

                <div class="status-card">
                    <h3>신청 현황</h3>
                    <p class="status-value">
                        <?php echo $current_batch_count; ?> / <?php echo $settings['max_capacity']; ?>명
                    </p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo ($current_batch_count / $settings['max_capacity']) * 100; ?>%"></div>
                    </div>
                </div>

                <div class="status-card">
                    <h3>모집 상태</h3>
                    <p class="status-value">
                        <?php echo $settings['is_active'] ? '✅ 진행중' : '❌ 마감'; ?>
                    </p>
                </div>
            </div>

            <!-- 설정 폼 -->
            <form id="enrollment-settings-form" class="donlinee-settings-form">
                <h2>모집 설정</h2>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="mode">현재 모드</label>
                        </th>
                        <td>
                            <select name="mode" id="mode">
                                <option value="waitlist" <?php selected($settings['mode'], 'waitlist'); ?>>대기 신청</option>
                                <option value="enrollment" <?php selected($settings['mode'], 'enrollment'); ?>>수강 신청</option>
                            </select>
                            <p class="description">현재 운영 모드를 선택하세요.</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="batch_number">기수</label>
                        </th>
                        <td>
                            <input type="number" name="batch_number" id="batch_number"
                                   value="<?php echo esc_attr($settings['batch_number']); ?>" min="1">
                            <p class="description">현재 모집 기수를 입력하세요.</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="start_date">모집 시작일</label>
                        </th>
                        <td>
                            <input type="datetime-local" name="start_date" id="start_date"
                                   value="<?php echo date('Y-m-d\TH:i', strtotime($settings['start_date'])); ?>">
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="end_date">모집 종료일</label>
                        </th>
                        <td>
                            <input type="datetime-local" name="end_date" id="end_date"
                                   value="<?php echo date('Y-m-d\TH:i', strtotime($settings['end_date'])); ?>">
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="auto_switch_date">자동 전환 시간</label>
                        </th>
                        <td>
                            <input type="datetime-local" name="auto_switch_date" id="auto_switch_date"
                                   value="<?php echo date('Y-m-d\TH:i', strtotime($settings['auto_switch_date'])); ?>">
                            <p class="description">대기 신청에서 수강 신청으로 자동 전환될 시간입니다. (예: 2025-12-13 11:00)</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="max_capacity">선발 예정 인원</label>
                        </th>
                        <td>
                            <input type="number" name="max_capacity" id="max_capacity"
                                   value="<?php echo esc_attr($settings['max_capacity']); ?>" min="1">
                            <p class="description">선발 예정 인원입니다. (표시용 - 이 인원을 초과해도 계속 신청받습니다)</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="is_active">모집 활성화</label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                       <?php checked($settings['is_active'], true); ?>>
                                모집 진행중
                            </label>
                            <p class="description">체크 해제 시 모집이 마감됩니다.</p>
                        </td>
                    </tr>
                </table>

                <h2 style="margin-top: 40px;">버튼 텍스트 관리</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row" colspan="2">
                            <h3 style="margin: 0; color: #23282d;">대기 신청 모드 텍스트</h3>
                        </th>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="waitlist_button_text">버튼 텍스트</label>
                        </th>
                        <td>
                            <input type="text" name="waitlist_button_text" id="waitlist_button_text"
                                   value="<?php echo esc_attr($settings['waitlist_button_text'] ?? '수강 대기신청'); ?>"
                                   style="width: 300px;">
                            <p class="description">대기 신청 모드에서 표시될 버튼 텍스트</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="countdown_text_waitlist">카운트다운 텍스트</label>
                        </th>
                        <td>
                            <input type="text" name="countdown_text_waitlist" id="countdown_text_waitlist"
                                   value="<?php echo esc_attr($settings['countdown_text_waitlist'] ?? '모집 시작까지'); ?>"
                                   style="width: 300px;">
                            <p class="description">대기 신청 모드에서 카운트다운 타이머 위에 표시될 텍스트</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" colspan="2">
                            <h3 style="margin: 20px 0 0 0; color: #23282d;">수강 신청 모드 텍스트</h3>
                        </th>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="enrollment_button_text">버튼 텍스트</label>
                        </th>
                        <td>
                            <input type="text" name="enrollment_button_text" id="enrollment_button_text"
                                   value="<?php echo esc_attr($settings['enrollment_button_text'] ?? '(OPEN) 수강 신청하기'); ?>"
                                   style="width: 300px;">
                            <p class="description">수강 신청 모드에서 표시될 버튼 텍스트</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="countdown_text_enrollment">카운트다운 텍스트</label>
                        </th>
                        <td>
                            <input type="text" name="countdown_text_enrollment" id="countdown_text_enrollment"
                                   value="<?php echo esc_attr($settings['countdown_text_enrollment'] ?? '모집 마감까지'); ?>"
                                   style="width: 300px;">
                            <p class="description">수강 신청 모드에서 카운트다운 타이머 위에 표시될 텍스트</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"></th>
                        <td>
                            <button type="button" id="reset-texts-default" class="button button-secondary">
                                기본 텍스트로 복원
                            </button>
                            <p class="description" style="margin-top: 10px;">
                                <strong>💡 참고:</strong> 텍스트 변경 후 저장하면 사이트 전체에 즉시 반영됩니다.
                            </p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary">설정 저장</button>
                    <span class="spinner" style="display: none;"></span>
                    <span class="save-message" style="display: none; color: green; margin-left: 10px;">✅ 저장되었습니다!</span>
                </p>
            </form>

            <!-- 자동화 설정 안내 -->
            <div class="donlinee-info-box">
                <h3>⚡ 자동화 기능 안내</h3>
                <ul>
                    <li>✅ 설정된 시간에 자동으로 모드가 전환됩니다.</li>
                    <li>✅ 선발 예정 인원(20명)을 초과해도 계속 신청받습니다.</li>
                    <li>✅ 모집 종료일이 되면 자동으로 마감됩니다.</li>
                    <li>✅ 모든 변경사항은 Slack으로 알림이 발송됩니다.</li>
                </ul>
            </div>
        </div>
        <?php
    }

    /**
     * 신청 목록 페이지 렌더링
     */
    public function render_enrollments_page() {
        $settings = Donlinee_Enrollment_Settings::get_current_settings();
        $current_status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $per_page = 20;

        $args = array(
            'payment_status' => $current_status,
            'batch_number' => $settings['batch_number'],
            'limit' => $per_page,
            'offset' => ($current_page - 1) * $per_page
        );

        $enrollments = Donlinee_Enrollment_Database::get_all_enrollments($args);
        $total_count = Donlinee_Enrollment_Database::get_total_count($current_status, $settings['batch_number']);
        $total_pages = ceil($total_count / $per_page);

        // 통계
        $stats = array(
            'total' => Donlinee_Enrollment_Database::get_total_count('', $settings['batch_number']),
            'submitted' => Donlinee_Enrollment_Database::get_total_count('submitted', $settings['batch_number']),
            'payment_pending' => Donlinee_Enrollment_Database::get_total_count('payment_pending', $settings['batch_number']),
            'paid' => Donlinee_Enrollment_Database::get_total_count('paid', $settings['batch_number']),
            'cancelled' => Donlinee_Enrollment_Database::get_total_count('cancelled', $settings['batch_number'])
        );
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">수강 신청 목록 (<?php echo $settings['batch_number']; ?>기)</h1>
            <a href="#" class="page-title-action" id="export-enrollments">CSV 내보내기</a>

            <hr class="wp-header-end">

            <!-- 통계 카드 -->
            <div class="donlinee-stats-cards">
                <div class="stats-card">
                    <h3>전체 신청</h3>
                    <p class="stats-number"><?php echo number_format($stats['total']); ?></p>
                </div>
                <div class="stats-card">
                    <h3>신청 완료</h3>
                    <p class="stats-number submitted"><?php echo number_format($stats['submitted']); ?></p>
                </div>
                <div class="stats-card">
                    <h3>결제 대기</h3>
                    <p class="stats-number pending"><?php echo number_format($stats['payment_pending']); ?></p>
                </div>
                <div class="stats-card">
                    <h3>결제 완료</h3>
                    <p class="stats-number paid"><?php echo number_format($stats['paid']); ?></p>
                </div>
                <div class="stats-card">
                    <h3>취소</h3>
                    <p class="stats-number cancelled"><?php echo number_format($stats['cancelled']); ?></p>
                </div>
            </div>

            <!-- 필터 -->
            <div class="tablenav top">
                <div class="alignleft actions">
                    <select name="status" id="status-filter">
                        <option value="">전체 상태</option>
                        <option value="submitted" <?php selected($current_status, 'submitted'); ?>>신청 완료</option>
                        <option value="payment_pending" <?php selected($current_status, 'payment_pending'); ?>>결제 대기</option>
                        <option value="paid" <?php selected($current_status, 'paid'); ?>>결제 완료</option>
                        <option value="cancelled" <?php selected($current_status, 'cancelled'); ?>>취소</option>
                    </select>
                    <input type="button" class="button" value="필터" id="apply-filter">
                </div>
            </div>

            <!-- 신청 목록 테이블 -->
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th width="3%">ID</th>
                        <th width="7%">이름</th>
                        <th width="8%">나이/성별</th>
                        <th width="10%">연락처</th>
                        <th width="20%">자기소개</th>
                        <th width="15%">판매경험</th>
                        <th width="8%">결제방법</th>
                        <th width="8%">상태</th>
                        <th width="10%">신청일시</th>
                        <th width="11%">작업</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($enrollments)) : ?>
                        <tr>
                            <td colspan="10" style="text-align: center;">신청 내역이 없습니다.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($enrollments as $enrollment) : ?>
                            <tr data-id="<?php echo esc_attr($enrollment['id']); ?>">
                                <td><?php echo esc_html($enrollment['id']); ?></td>
                                <td><?php echo esc_html($enrollment['name']); ?></td>
                                <td><?php echo esc_html($enrollment['age_gender']); ?></td>
                                <td><?php echo esc_html($enrollment['phone']); ?></td>
                                <td>
                                    <span class="truncate" title="<?php echo esc_attr($enrollment['self_intro']); ?>">
                                        <?php echo esc_html(mb_substr($enrollment['self_intro'], 0, 50)); ?>...
                                    </span>
                                    <button class="button button-small view-details" data-id="<?php echo esc_attr($enrollment['id']); ?>">상세보기</button>
                                </td>
                                <td>
                                    <span class="truncate" title="<?php echo esc_attr($enrollment['sales_experience']); ?>">
                                        <?php echo esc_html(mb_substr($enrollment['sales_experience'], 0, 30)); ?>...
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    if ($enrollment['payment_method'] === 'transfer') {
                                        echo '계좌이체';
                                    } elseif ($enrollment['payment_method'] === 'card') {
                                        echo '카드결제';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <select class="enrollment-status-select" data-id="<?php echo esc_attr($enrollment['id']); ?>">
                                        <option value="submitted" <?php selected($enrollment['payment_status'], 'submitted'); ?>>신청완료</option>
                                        <option value="payment_pending" <?php selected($enrollment['payment_status'], 'payment_pending'); ?>>결제대기</option>
                                        <option value="paid" <?php selected($enrollment['payment_status'], 'paid'); ?>>결제완료</option>
                                        <option value="cancelled" <?php selected($enrollment['payment_status'], 'cancelled'); ?>>취소</option>
                                    </select>
                                </td>
                                <td><?php echo esc_html($enrollment['created_at']); ?></td>
                                <td>
                                    <button class="button button-small delete-enrollment-btn" data-id="<?php echo esc_attr($enrollment['id']); ?>">삭제</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- 페이징 -->
            <?php if ($total_pages > 1) : ?>
                <div class="tablenav bottom">
                    <div class="tablenav-pages">
                        <span class="displaying-num"><?php echo $total_count; ?>개 항목</span>
                        <span class="pagination-links">
                            <?php
                            $base_url = admin_url('admin.php?page=donlinee-enrollment-list');
                            if ($current_status) {
                                $base_url .= '&status=' . $current_status;
                            }

                            if ($current_page > 1) {
                                echo '<a class="prev-page" href="' . $base_url . '&paged=' . ($current_page - 1) . '">‹</a>';
                            }

                            for ($i = 1; $i <= $total_pages; $i++) {
                                if ($i == $current_page) {
                                    echo '<span class="current">' . $i . '</span>';
                                } else {
                                    echo '<a href="' . $base_url . '&paged=' . $i . '">' . $i . '</a>';
                                }
                            }

                            if ($current_page < $total_pages) {
                                echo '<a class="next-page" href="' . $base_url . '&paged=' . ($current_page + 1) . '">›</a>';
                            }
                            ?>
                        </span>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- 상세보기 모달 -->
        <div id="enrollment-detail-modal" style="display: none;">
            <div class="modal-content">
                <h2>신청 상세 정보</h2>
                <div id="enrollment-detail-content"></div>
                <button class="button button-primary" onclick="closeDetailModal()">닫기</button>
            </div>
        </div>
        <?php
    }

    /**
     * 통계 페이지 렌더링
     */
    public function render_stats_page() {
        $settings = Donlinee_Enrollment_Settings::get_current_settings();

        // 날짜별 통계 가져오기
        global $wpdb;
        $table_name = Donlinee_Enrollment_Database::get_enrollments_table_name();

        $daily_stats = $wpdb->get_results(
            $wpdb->prepare("
                SELECT DATE(created_at) as date, COUNT(*) as count
                FROM $table_name
                WHERE batch_number = %d
                  AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY DATE(created_at)
                ORDER BY date DESC
            ", $settings['batch_number']),
            ARRAY_A
        );

        // 결제 방법별 통계
        $payment_stats = $wpdb->get_results(
            $wpdb->prepare("
                SELECT payment_method, COUNT(*) as count
                FROM $table_name
                WHERE batch_number = %d
                  AND payment_method IS NOT NULL
                GROUP BY payment_method
            ", $settings['batch_number']),
            ARRAY_A
        );
        ?>
        <div class="wrap">
            <h1>수강 신청 통계 (<?php echo $settings['batch_number']; ?>기)</h1>

            <div class="donlinee-stats-container">
                <h2>최근 30일 신청 현황</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>날짜</th>
                            <th>신청 수</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daily_stats)) : ?>
                            <tr>
                                <td colspan="2" style="text-align: center;">데이터가 없습니다.</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($daily_stats as $stat) : ?>
                                <tr>
                                    <td><?php echo esc_html($stat['date']); ?></td>
                                    <td><?php echo esc_html($stat['count']); ?>건</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <h2 style="margin-top: 40px;">결제 방법별 통계</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>결제 방법</th>
                            <th>신청 수</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($payment_stats)) : ?>
                            <tr>
                                <td colspan="2" style="text-align: center;">데이터가 없습니다.</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($payment_stats as $stat) : ?>
                                <tr>
                                    <td>
                                        <?php
                                        if ($stat['payment_method'] === 'transfer') {
                                            echo '계좌이체';
                                        } elseif ($stat['payment_method'] === 'card') {
                                            echo '카드결제';
                                        } else {
                                            echo '미선택';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo esc_html($stat['count']); ?>건</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    /**
     * AJAX: 설정 저장
     */
    public function ajax_save_settings() {
        check_ajax_referer('donlinee-enrollment-admin-nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('권한이 없습니다.');
        }

        $settings = array(
            'mode' => sanitize_text_field($_POST['mode']),
            'batch_number' => intval($_POST['batch_number']),
            'start_date' => sanitize_text_field($_POST['start_date']),
            'end_date' => sanitize_text_field($_POST['end_date']),
            'auto_switch_date' => sanitize_text_field($_POST['auto_switch_date']),
            'max_capacity' => intval($_POST['max_capacity']),
            'is_active' => isset($_POST['is_active']) ? 'true' : 'false',
            // 버튼 텍스트 설정
            'waitlist_button_text' => sanitize_text_field($_POST['waitlist_button_text']),
            'enrollment_button_text' => sanitize_text_field($_POST['enrollment_button_text']),
            'countdown_text_waitlist' => sanitize_text_field($_POST['countdown_text_waitlist']),
            'countdown_text_enrollment' => sanitize_text_field($_POST['countdown_text_enrollment'])
        );

        $result = Donlinee_Enrollment_Settings::update_settings($settings);

        if ($result) {
            wp_send_json_success('설정이 저장되었습니다.');
        } else {
            wp_send_json_error('설정 저장 중 오류가 발생했습니다.');
        }
    }

    /**
     * AJAX: 모드 전환
     */
    public function ajax_switch_mode() {
        check_ajax_referer('donlinee-enrollment-admin-nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('권한이 없습니다.');
        }

        $current_mode = Donlinee_Enrollment_Settings::get_setting('mode');
        $new_mode = $current_mode === 'enrollment' ? 'waitlist' : 'enrollment';

        $result = Donlinee_Enrollment_Settings::update_mode($new_mode);

        if ($result) {
            wp_send_json_success(array(
                'message' => '모드가 전환되었습니다.',
                'new_mode' => $new_mode
            ));
        } else {
            wp_send_json_error('모드 전환 중 오류가 발생했습니다.');
        }
    }

    /**
     * AJAX: 신청자 상태 업데이트
     */
    public function ajax_update_enrollment_status() {
        check_ajax_referer('donlinee-enrollment-admin-nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('권한이 없습니다.');
        }

        $id = intval($_POST['id']);
        $status = sanitize_text_field($_POST['status']);

        $result = Donlinee_Enrollment_Database::update_payment_status($id, $status);

        if ($result) {
            wp_send_json_success('상태가 업데이트되었습니다.');
        } else {
            wp_send_json_error('상태 업데이트 중 오류가 발생했습니다.');
        }
    }

    /**
     * AJAX: 신청 삭제
     */
    public function ajax_delete_enrollment() {
        check_ajax_referer('donlinee-enrollment-admin-nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('권한이 없습니다.');
        }

        $id = intval($_POST['id']);

        $result = Donlinee_Enrollment_Database::delete_enrollment($id);

        if ($result) {
            wp_send_json_success('삭제되었습니다.');
        } else {
            wp_send_json_error('삭제 중 오류가 발생했습니다.');
        }
    }

    /**
     * AJAX: CSV 내보내기
     */
    public function ajax_export_enrollments() {
        check_ajax_referer('donlinee-enrollment-admin-nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('권한이 없습니다.');
        }

        $settings = Donlinee_Enrollment_Settings::get_current_settings();
        $export_data = Donlinee_Enrollment_Database::get_export_data($settings['batch_number']);

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="enrollments_batch_' . $settings['batch_number'] . '_' . date('Ymd') . '.csv"');

        // BOM 추가 (Excel에서 UTF-8 인식)
        echo "\xEF\xBB\xBF";

        $output = fopen('php://output', 'w');
        foreach ($export_data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }
}