<?php
/**
 * Theme functions and definitions
 *
 * @package Donlinee
 * @author webdot
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Theme setup
 */
function donlinee_setup() {
    // Add theme support
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'automatic-feed-links' );

    // Register navigation menus
    register_nav_menus( array(
        'primary' => esc_html__( 'Primary Menu', 'donlinee' ),
    ) );
}
add_action( 'after_setup_theme', 'donlinee_setup' );

/**
 * Enqueue styles
 */
function donlinee_enqueue_styles() {
    wp_enqueue_style( 'donlinee-style', get_stylesheet_uri(), array(), '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'donlinee_enqueue_styles' );

/**
 * Create applications table
 */
function create_applications_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'applications';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(50) NOT NULL,
        age tinyint(3) NOT NULL,
        phone varchar(13) NOT NULL,
        course varchar(100) DEFAULT NULL,
        status varchar(20) DEFAULT 'pending',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        sent_at datetime DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY phone_course (phone, course),
        KEY status (status),
        KEY created_at (created_at)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
// 테마 활성화시 테이블 생성
add_action('after_switch_theme', 'create_applications_table');

/**
 * Admin menu for applications
 */
function applications_admin_menu() {
    add_menu_page(
        '강의 접수자 관리',
        '접수자 관리',
        'manage_options',
        'applications',
        'applications_admin_page',
        'dashicons-groups',
        30
    );
}
add_action('admin_menu', 'applications_admin_menu');

/**
 * Admin page display
 */
function applications_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'applications';

    // CSV 내보내기 처리
    if (isset($_GET['export']) && $_GET['export'] === 'csv') {
        export_applications_csv();
        return;
    }

    $applicants = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
    ?>
    <div class="wrap">
        <h1>강의 접수자 관리 (총 <?php echo count($applicants); ?>명)</h1>

        <!-- Slack 설정 상태 표시 -->
        <div style="margin: 20px 0; padding: 15px; background: #f0f0f1; border-left: 4px solid <?php echo (defined('SLACK_WEBHOOK_URL') && SLACK_WEBHOOK_URL) ? '#00ba37' : '#d63638'; ?>;">
            <h3 style="margin-top: 0;">📢 Slack 알림 설정 상태</h3>
            <?php if (defined('SLACK_WEBHOOK_URL') && SLACK_WEBHOOK_URL): ?>
                <p style="color: #00ba37;">✅ Slack 알림이 <strong>활성화</strong> 되어 있습니다.</p>
                <p>채널: <code><?php echo defined('SLACK_CHANNEL') ? SLACK_CHANNEL : '기본 채널'; ?></code></p>
                <button id="test-slack" class="button button-secondary">
                    Slack 테스트 알림 보내기
                </button>
            <?php else: ?>
                <p style="color: #d63638;">❌ Slack Webhook URL이 설정되지 않았습니다.</p>
                <p><strong>설정 방법:</strong></p>
                <ol>
                    <li>Slack 워크스페이스에서 Incoming Webhook 앱 추가</li>
                    <li>Webhook URL 생성</li>
                    <li><code>wp-config-custom.php</code> 파일의 25번째 줄에 URL 입력</li>
                </ol>
            <?php endif; ?>
        </div>

        <!-- 배치 발송 버튼 -->
        <div style="margin: 20px 0;">
            <button id="batch-send" class="button button-primary button-large">
                선택된 명단에 강의오픈 알림톡 발송
            </button>
            <a href="?page=applications&export=csv" class="button button-secondary">
                엑셀 다운로드
            </a>
        </div>

        <!-- 명단 테이블 -->
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width: 50px;">
                        <input type="checkbox" id="select-all">
                    </th>
                    <th>이름</th>
                    <th>나이</th>
                    <th>전화번호</th>
                    <th>강의</th>
                    <th>상태</th>
                    <th>접수일</th>
                    <th>발송일</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($applicants as $row): ?>
            <tr>
                <td>
                    <input type="checkbox" class="batch-select"
                           value="<?php echo esc_attr($row->phone); ?>"
                           data-name="<?php echo esc_attr($row->name); ?>"
                           data-id="<?php echo esc_attr($row->id); ?>">
                </td>
                <td><?php echo esc_html($row->name); ?></td>
                <td><?php echo esc_html($row->age); ?></td>
                <td><?php echo esc_html($row->phone); ?></td>
                <td><?php echo esc_html($row->course ?: '돈마고치'); ?></td>
                <td>
                    <span class="status-<?php echo esc_attr($row->status); ?>"
                          style="padding: 3px 8px; border-radius: 3px; background: <?php
                          echo $row->status === 'sent' ? '#d4edda' :
                              ($row->status === 'pending' ? '#fff3cd' : '#f8d7da'); ?>">
                        <?php echo esc_html($row->status); ?>
                    </span>
                </td>
                <td><?php echo esc_html($row->created_at); ?></td>
                <td><?php echo $row->sent_at ? esc_html($row->sent_at) : '-'; ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <!-- 진행상황 표시 -->
        <div id="batch-progress" style="display:none; margin-top:20px; padding: 15px; background: #f1f1f1; border-radius: 5px;">
            <div class="progress-bar" style="background: #fff; height: 30px; border-radius: 3px; position: relative;">
                <div id="progress-fill" style="background: #0073aa; height: 100%; width: 0%; border-radius: 3px; transition: width 0.3s;"></div>
                <span id="progress-text" style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); font-weight: bold;">0/0</span>
            </div>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        $('#select-all').click(function() {
            $('.batch-select').prop('checked', this.checked);
        });

        // Slack 테스트 알림 버튼
        $('#test-slack').click(function() {
            if(!confirm('테스트 알림을 Slack에 발송하시겠습니까?')) {
                return;
            }

            var $btn = $(this);
            $btn.prop('disabled', true).text('발송 중...');

            $.post(ajaxurl, {
                action: 'test_slack_notification',
                nonce: '<?php echo wp_create_nonce('test_slack'); ?>'
            }, function(res) {
                if(res.success) {
                    alert('✅ Slack 테스트 알림이 성공적으로 발송되었습니다!');
                } else {
                    alert('❌ Slack 알림 발송 실패: ' + (res.data.message || '알 수 없는 오류'));
                }
            }).fail(function() {
                alert('❌ 요청 중 오류가 발생했습니다.');
            }).always(function() {
                $btn.prop('disabled', false).text('Slack 테스트 알림 보내기');
            });
        });

        $('#batch-send').click(function() {
            var selected = $('.batch-select:checked');
            if(selected.length === 0) {
                alert('발송할 명단을 선택하세요.');
                return;
            }

            if(!confirm(selected.length + '명에게 알림톡을 발송하시겠습니까?')) {
                return;
            }

            var total = selected.length;
            var sent = 0;

            $('#batch-progress').show();

            selected.each(function(i) {
                var phone = $(this).val();
                var name = $(this).data('name');
                var id = $(this).data('id');

                $.post(ajaxurl, {
                    action: 'batch_alimtalk',
                    id: id,
                    phone: phone,
                    name: name,
                    template: 'TP_강의오픈_001',
                    nonce: '<?php echo wp_create_nonce('batch_alimtalk'); ?>'
                }, function(res) {
                    sent++;
                    var percent = Math.round((sent / total) * 100);
                    $('#progress-fill').css('width', percent + '%');
                    $('#progress-text').text(sent + '/' + total);

                    if(sent === total) {
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    }
                }).fail(function() {
                    sent++;
                    $('#progress-text').text(sent + '/' + total + ' (에러 발생)');
                });
            });
        });
    });
    </script>
    <?php
}

/**
 * Export CSV function
 */
function export_applications_csv() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'applications';
    $applicants = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="applications_' . date('Ymd') . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // BOM for Excel UTF-8 support
    fputs($output, "\xEF\xBB\xBF");

    // Header row
    fputcsv($output, ['이름', '나이', '전화번호', '강의', '상태', '접수일', '발송일']);

    // Data rows
    foreach($applicants as $row) {
        fputcsv($output, [
            $row->name,
            $row->age,
            $row->phone,
            $row->course ?: '돈마고치',
            $row->status,
            $row->created_at,
            $row->sent_at ?: ''
        ]);
    }

    fclose($output);
    exit;
}

/**
 * REST API endpoints
 */
add_action('rest_api_init', function() {
    register_rest_route('donlinee/v1', '/submit', [
        'methods' => 'POST',
        'callback' => 'handle_application_submission',
        'permission_callback' => '__return_true'
    ]);
});

/**
 * Handle form submission
 */
function handle_application_submission($request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'applications';

    // Validate and sanitize input
    $name = sanitize_text_field($request['name']);
    $age = intval($request['age']);
    $phone = sanitize_text_field($request['phone']);
    $course = sanitize_text_field($request['course'] ?: '돈마고치');

    // Validation
    if(empty($name) || empty($phone) || $age < 1) {
        return new WP_Error('invalid_data', '필수 정보를 모두 입력해주세요.', ['status' => 400]);
    }

    // Phone number format validation
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if(strlen($phone) < 10 || strlen($phone) > 11) {
        return new WP_Error('invalid_phone', '올바른 전화번호를 입력해주세요.', ['status' => 400]);
    }

    // Check for duplicate
    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE phone = %s AND course = %s",
        $phone, $course
    ));

    if($existing) {
        return new WP_Error('duplicate', '이미 접수된 전화번호입니다.', ['status' => 400]);
    }

    // Insert to database
    $result = $wpdb->insert($table_name, [
        'name' => $name,
        'age' => $age,
        'phone' => $phone,
        'course' => $course,
        'status' => 'pending',
        'created_at' => current_time('mysql')
    ]);

    if($result === false) {
        return new WP_Error('db_error', '접수 중 오류가 발생했습니다.', ['status' => 500]);
    }

    // Send immediate notification
    $alimtalk_result = send_alimtalk($phone, 'TP_접수완료_001', [
        'name' => $name,
        'course_name' => $course
    ]);

    // Send Slack notification for new application
    $slack_data = [
        'name' => $name,
        'age' => $age,
        'phone' => $phone,
        'course' => $course
    ];

    $slack_result = send_slack_notification($slack_data);

    // Log if Slack notification failed (but don't fail the application)
    if (!$slack_result) {
        error_log('Slack notification failed for application: ' . $name . ' (' . $phone . ')');
    }

    return [
        'success' => true,
        'message' => '접수가 완료되었습니다. 카카오톡으로 안내 메시지가 발송됩니다.',
        'data' => [
            'name' => $name,
            'phone' => $phone
        ]
    ];
}

/**
 * Send Slack notification for new applications
 */
function send_slack_notification($application_data) {
    // Check if Slack notifications are enabled
    if (!defined('SLACK_NOTIFICATIONS_ENABLED') || !SLACK_NOTIFICATIONS_ENABLED) {
        return false;
    }

    // Get Slack webhook URL
    $webhook_url = defined('SLACK_WEBHOOK_URL') ? SLACK_WEBHOOK_URL : get_option('slack_webhook_url');

    if (empty($webhook_url)) {
        error_log('Slack Webhook URL not configured');
        return false;
    }

    // Format the message
    $message = [
        // channel을 지정하지 않으면 Webhook의 기본 채널로 전송됨
        // 'channel' => defined('SLACK_CHANNEL') ? SLACK_CHANNEL : null,
        'username' => '돈린이 수강신청 알림',
        'icon_emoji' => ':bell:',
        'attachments' => [
            [
                'color' => '#36a64f', // Green color for success
                'pretext' => '🎉 새로운 수강 신청이 접수되었습니다!',
                'title' => '수강 신청 정보',
                'title_link' => admin_url('admin.php?page=applications'),
                'fields' => [
                    [
                        'title' => '이름',
                        'value' => $application_data['name'],
                        'short' => true
                    ],
                    [
                        'title' => '나이',
                        'value' => $application_data['age'] . '세',
                        'short' => true
                    ],
                    [
                        'title' => '전화번호',
                        'value' => $application_data['phone'],
                        'short' => true
                    ],
                    [
                        'title' => '강의',
                        'value' => $application_data['course'] ?: '돈마고치',
                        'short' => true
                    ],
                    [
                        'title' => '접수 시간',
                        'value' => current_time('Y-m-d H:i:s'),
                        'short' => false
                    ]
                ],
                'footer' => '돈린이 관리 시스템',
                'footer_icon' => 'https://platform.slack-edge.com/img/default_application_icon.png',
                'ts' => time()
            ]
        ]
    ];

    // Channel은 이미 주석 처리되었으므로 제거할 필요 없음
    // Remove channel if not set (will use webhook's default channel)
    // if (empty($message['channel'])) {
    //     unset($message['channel']);
    // }

    // Send the webhook request
    $response = wp_remote_post($webhook_url, [
        'headers' => [
            'Content-Type' => 'application/json',
        ],
        'body' => json_encode($message),
        'timeout' => 30
    ]);

    if (is_wp_error($response)) {
        error_log('Slack notification error: ' . $response->get_error_message());
        return false;
    }

    $response_body = wp_remote_retrieve_body($response);
    $response_code = wp_remote_retrieve_response_code($response);

    if ($response_code !== 200) {
        error_log('Slack notification failed with code ' . $response_code . ': ' . $response_body);
        return false;
    }

    return true;
}

/**
 * Send test Slack notification (for admin testing)
 */
function send_test_slack_notification() {
    $test_data = [
        'name' => '테스트',
        'age' => 30,
        'phone' => '010-1234-5678',
        'course' => '돈마고치 (테스트)'
    ];

    return send_slack_notification($test_data);
}

/**
 * NHN Cloud AlimTalk function
 */
function send_alimtalk($phone, $templateCode, $variables = []) {
    // Get API keys from environment or options
    $appKey = defined('NHN_APP_KEY') ? NHN_APP_KEY : get_option('nhn_app_key');
    $secretKey = defined('NHN_SECRET_KEY') ? NHN_SECRET_KEY : get_option('nhn_secret_key');
    $senderKey = defined('NHN_SENDER_KEY') ? NHN_SENDER_KEY : get_option('nhn_sender_key');

    if(empty($appKey) || empty($secretKey) || empty($senderKey)) {
        error_log('NHN Cloud API keys not configured');
        return false;
    }

    // Format phone number
    $phone = preg_replace('/[^0-9]/', '', $phone);

    // Prepare request data
    $data = [
        'senderKey' => $senderKey,
        'templateCode' => $templateCode,
        'recipientList' => [[
            'recipientNo' => $phone,
            'templateParameter' => $variables
        ]]
    ];

    // Send API request
    $response = wp_remote_post(
        "https://api-alimtalk.cloud.toast.com/alimtalk/v2.3/appKeys/{$appKey}/messages",
        [
            'headers' => [
                'Content-Type' => 'application/json;charset=UTF-8',
                'X-Secret-Key' => $secretKey
            ],
            'body' => json_encode($data),
            'timeout' => 30
        ]
    );

    if(is_wp_error($response)) {
        error_log('AlimTalk API error: ' . $response->get_error_message());
        return false;
    }

    $body = wp_remote_retrieve_body($response);
    $result = json_decode($body, true);

    if($result['header']['isSuccessful'] ?? false) {
        return true;
    } else {
        error_log('AlimTalk API failed: ' . $body);
        return false;
    }
}

/**
 * AJAX handler for test Slack notification
 */
add_action('wp_ajax_test_slack_notification', 'handle_test_slack_notification');
function handle_test_slack_notification() {
    // Verify nonce
    if(!wp_verify_nonce($_POST['nonce'], 'test_slack')) {
        wp_send_json_error(['message' => '보안 검증 실패']);
        return;
    }

    // Check permissions
    if(!current_user_can('manage_options')) {
        wp_send_json_error(['message' => '권한이 없습니다.']);
        return;
    }

    // Send test notification
    $result = send_test_slack_notification();

    if($result) {
        wp_send_json_success(['message' => '테스트 알림 발송 완료']);
    } else {
        wp_send_json_error(['message' => 'Slack 알림 발송 실패 - Webhook URL을 확인하세요']);
    }
}

/**
 * AJAX handler for batch AlimTalk
 */
add_action('wp_ajax_batch_alimtalk', 'handle_batch_alimtalk');
function handle_batch_alimtalk() {
    // Verify nonce
    if(!wp_verify_nonce($_POST['nonce'], 'batch_alimtalk')) {
        wp_die('보안 검증 실패');
    }

    // Check permissions
    if(!current_user_can('manage_options')) {
        wp_die('권한이 없습니다.');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'applications';

    $id = intval($_POST['id']);
    $phone = sanitize_text_field($_POST['phone']);
    $name = sanitize_text_field($_POST['name']);
    $template = sanitize_text_field($_POST['template']);

    // Send AlimTalk
    $result = send_alimtalk($phone, $template, [
        'name' => $name,
        'course_start_date' => date('Y년 m월 d일')
    ]);

    if($result) {
        // Update status
        $wpdb->update(
            $table_name,
            [
                'status' => 'sent',
                'sent_at' => current_time('mysql')
            ],
            ['id' => $id]
        );

        wp_send_json_success(['message' => '발송 완료']);
    } else {
        wp_send_json_error(['message' => '발송 실패']);
    }
}
