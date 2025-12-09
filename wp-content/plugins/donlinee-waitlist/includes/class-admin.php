<?php
/**
 * 관리자 페이지 클래스
 */

if (!defined('ABSPATH')) {
    exit;
}

class Donlinee_Waitlist_Admin {

    public function __construct() {
        // 관리자 메뉴 추가
        add_action('admin_menu', array($this, 'add_admin_menu'));

        // 관리자 스크립트 및 스타일 로드
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    /**
     * 관리자 메뉴 추가
     */
    public function add_admin_menu() {
        add_menu_page(
            '수강 대기 신청 관리',
            '수강 대기 신청',
            'manage_options',
            'donlinee-waitlist',
            array($this, 'render_admin_page'),
            'dashicons-list-view',
            30
        );

        // 서브메뉴 추가
        add_submenu_page(
            'donlinee-waitlist',
            '신청 목록',
            '신청 목록',
            'manage_options',
            'donlinee-waitlist',
            array($this, 'render_admin_page')
        );

        add_submenu_page(
            'donlinee-waitlist',
            '통계',
            '통계',
            'manage_options',
            'donlinee-waitlist-stats',
            array($this, 'render_stats_page')
        );
    }

    /**
     * 관리자 스크립트 및 스타일 로드
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'donlinee-waitlist') === false) {
            return;
        }

        // 관리자 CSS
        wp_enqueue_style(
            'donlinee-waitlist-admin',
            DONLINEE_WAITLIST_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            DONLINEE_WAITLIST_VERSION
        );

        // 관리자 JavaScript
        wp_enqueue_script(
            'donlinee-waitlist-admin',
            DONLINEE_WAITLIST_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            DONLINEE_WAITLIST_VERSION,
            true
        );

        // AJAX 설정
        wp_localize_script('donlinee-waitlist-admin', 'donlinee_waitlist_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('donlinee-waitlist-admin-nonce'),
            'export_url' => admin_url('admin-ajax.php?action=donlinee_waitlist_export&nonce=' . wp_create_nonce('donlinee-waitlist-export-nonce'))
        ));
    }

    /**
     * 신청 목록 페이지 렌더링
     */
    public function render_admin_page() {
        // 필터 및 페이징 파라미터
        $current_status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $per_page = 20;

        // 데이터 가져오기
        $args = array(
            'status' => $current_status,
            'limit' => $per_page,
            'offset' => ($current_page - 1) * $per_page
        );

        $applications = Donlinee_Waitlist_Database::get_all_applications($args);
        $total_count = Donlinee_Waitlist_Database::get_total_count($current_status);
        $total_pages = ceil($total_count / $per_page);

        // 통계
        $stats = array(
            'total' => Donlinee_Waitlist_Database::get_total_count(),
            'pending' => Donlinee_Waitlist_Database::get_total_count('pending'),
            'confirmed' => Donlinee_Waitlist_Database::get_total_count('confirmed'),
            'cancelled' => Donlinee_Waitlist_Database::get_total_count('cancelled')
        );

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">수강 대기 신청 관리</h1>
            <a href="#" class="page-title-action" id="export-csv">CSV 내보내기</a>

            <hr class="wp-header-end">

            <!-- Slack 설정 상태 표시 -->
            <div style="margin: 20px 0; padding: 15px; background: #f0f0f1; border-left: 4px solid <?php echo (defined('SLACK_WEBHOOK_URL') && SLACK_WEBHOOK_URL) ? '#00ba37' : '#d63638'; ?>;">
                <h3 style="margin-top: 0;">📢 Slack 알림 설정 상태</h3>
                <?php if (defined('SLACK_WEBHOOK_URL') && SLACK_WEBHOOK_URL): ?>
                    <p style="color: #00ba37;">✅ Slack 알림이 <strong>활성화</strong> 되어 있습니다.</p>
                    <p>채널: <code><?php echo defined('SLACK_CHANNEL') ? SLACK_CHANNEL : '기본 채널'; ?></code></p>
                    <p style="color: #666;">수강 대기 신청이 접수되면 Slack으로 알림이 발송됩니다.</p>

                    <?php if (defined('SLACK_DEBUG_MODE') && SLACK_DEBUG_MODE): ?>
                    <details style="margin-top: 10px;">
                        <summary style="cursor: pointer; color: #666;">🔧 디버그 정보 (클릭하여 펼치기)</summary>
                        <div style="margin-top: 10px; padding: 10px; background: #fff; border: 1px solid #ddd; font-family: monospace; font-size: 12px;">
                            <p>Webhook URL: <?php echo substr(SLACK_WEBHOOK_URL, 0, 50) . '...'; ?></p>
                            <p>설정 파일 로드: ✅ config.php</p>
                            <p>알림 활성화: <?php echo SLACK_NOTIFICATIONS_ENABLED ? '✅ Yes' : '❌ No'; ?></p>
                        </div>
                    </details>
                    <?php endif; ?>

                    <button id="test-slack-waitlist" class="button button-secondary" style="margin-top: 10px;">
                        Slack 테스트 알림 보내기
                    </button>
                <?php else: ?>
                    <p style="color: #d63638;">❌ Slack Webhook URL이 설정되지 않았습니다.</p>
                    <p><strong>설정 방법:</strong></p>
                    <ol>
                        <li>Slack 워크스페이스에서 Incoming Webhook 앱 추가</li>
                        <li>Webhook URL 생성</li>
                        <li>다음 중 하나의 파일에 URL 입력:
                            <ul style="margin-top: 5px;">
                                <li><code>/wp-content/plugins/donlinee-waitlist/config.php</code> (권장)</li>
                                <li><code>wp-config-custom.php</code></li>
                            </ul>
                        </li>
                    </ol>
                <?php endif; ?>
            </div>

            <!-- 통계 카드 -->
            <div class="donlinee-stats-cards">
                <div class="stats-card">
                    <h3>전체 신청</h3>
                    <p class="stats-number"><?php echo number_format($stats['total']); ?></p>
                </div>
                <div class="stats-card">
                    <h3>대기중</h3>
                    <p class="stats-number pending"><?php echo number_format($stats['pending']); ?></p>
                </div>
                <div class="stats-card">
                    <h3>확정</h3>
                    <p class="stats-number confirmed"><?php echo number_format($stats['confirmed']); ?></p>
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
                        <option value="pending" <?php selected($current_status, 'pending'); ?>>대기중</option>
                        <option value="confirmed" <?php selected($current_status, 'confirmed'); ?>>확정</option>
                        <option value="cancelled" <?php selected($current_status, 'cancelled'); ?>>취소</option>
                    </select>
                    <input type="button" class="button" value="필터" id="apply-filter">
                </div>
            </div>

            <!-- 신청 목록 테이블 -->
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="15%">이름</th>
                        <th width="15%">연락처</th>
                        <th width="20%">신청일시</th>
                        <th width="10%">상태</th>
                        <th width="15%">작업</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($applications)) : ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">신청 내역이 없습니다.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($applications as $app) : ?>
                            <tr data-id="<?php echo esc_attr($app['id']); ?>">
                                <td><?php echo esc_html($app['id']); ?></td>
                                <td><?php echo esc_html($app['name']); ?></td>
                                <td><?php echo esc_html($app['phone']); ?></td>
                                <td><?php echo esc_html($app['created_at']); ?></td>
                                <td>
                                    <select class="status-select" data-id="<?php echo esc_attr($app['id']); ?>">
                                        <option value="pending" <?php selected($app['status'], 'pending'); ?>>대기중</option>
                                        <option value="confirmed" <?php selected($app['status'], 'confirmed'); ?>>확정</option>
                                        <option value="cancelled" <?php selected($app['status'], 'cancelled'); ?>>취소</option>
                                    </select>
                                </td>
                                <td>
                                    <button class="button button-small delete-btn" data-id="<?php echo esc_attr($app['id']); ?>">삭제</button>
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
                            $base_url = admin_url('admin.php?page=donlinee-waitlist');
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
        <?php
    }

    /**
     * 통계 페이지 렌더링
     */
    public function render_stats_page() {
        // 날짜별 통계 가져오기
        global $wpdb;
        $table_name = Donlinee_Waitlist_Database::get_table_name();

        $daily_stats = $wpdb->get_results("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM $table_name
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date DESC
        ", ARRAY_A);

        ?>
        <div class="wrap">
            <h1>수강 대기 신청 통계</h1>

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
            </div>
        </div>
        <?php
    }
}