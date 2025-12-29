<?php
/**
 * Review Submission System Class
 *
 * @package DonlineeEbookFunnel
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class DonlineeReviewSubmission {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Register shortcodes
        add_shortcode('donlinee_review_form', array($this, 'render_review_form'));
        add_shortcode('donlinee_my_reviews', array($this, 'render_my_reviews'));

        // AJAX handlers
        add_action('wp_ajax_donlinee_submit_review', array($this, 'submit_review'));
        add_action('wp_ajax_donlinee_validate_blog_url', array($this, 'validate_blog_url'));
        add_action('wp_ajax_donlinee_check_review_status', array($this, 'check_review_status'));

        // Admin AJAX handlers
        add_action('wp_ajax_donlinee_approve_review', array($this, 'approve_review'));
        add_action('wp_ajax_donlinee_reject_review', array($this, 'reject_review'));

        // Register endpoint
        add_action('init', array($this, 'register_endpoints'));
    }

    /**
     * Register endpoints
     */
    public function register_endpoints() {
        add_rewrite_endpoint('submit-review', EP_PAGES);
        add_rewrite_endpoint('my-reviews', EP_PAGES);
    }

    /**
     * Render review submission form
     */
    public function render_review_form($atts = array()) {
        if (!is_user_logged_in()) {
            return '<p>후기를 작성하려면 로그인이 필요합니다. <a href="#" onclick="donlineeKakaoLogin()">카카오로 로그인</a></p>';
        }

        $user_id = get_current_user_id();

        // Check if user has completed the ebook
        $progress = $this->get_user_ebook_progress($user_id);

        if (!$progress || $progress->progress_percentage < 100) {
            return '<div class="alert alert-warning">
                <h3>아직 전자책을 완독하지 않으셨습니다.</h3>
                <p>후기를 작성하려면 먼저 전자책을 100% 완독해야 합니다.</p>
                <p>현재 진도: ' . ($progress ? number_format($progress->progress_percentage, 1) : '0.0') . '%</p>
                <a href="' . home_url('/ebook-viewer') . '" class="btn btn-primary">전자책 계속 읽기</a>
            </div>';
        }

        // Check if user already submitted a review
        $existing_review = $this->get_user_review($user_id);

        if ($existing_review) {
            return $this->render_review_status($existing_review);
        }

        ob_start();
        ?>
        <div class="review-submission-container">
            <h2>전자책 후기 작성</h2>

            <div class="progress-status">
                <div class="status-item completed">
                    <span class="status-icon">✓</span>
                    <span class="status-text">전자책 완독</span>
                </div>
                <div class="status-item active">
                    <span class="status-icon">2</span>
                    <span class="status-text">블로그 후기 작성</span>
                </div>
                <div class="status-item">
                    <span class="status-icon">3</span>
                    <span class="status-text">18만원 쿠폰 발급</span>
                </div>
            </div>

            <div class="review-guidelines">
                <h3>📝 후기 작성 가이드</h3>
                <ul>
                    <li>네이버 블로그, 티스토리, 개인 블로그 등에 작성하세요</li>
                    <li>최소 300자 이상 작성해주세요</li>
                    <li>"돈린이 100원 전자책" 키워드를 포함해주세요</li>
                    <li>실제 읽은 내용과 느낀 점을 솔직하게 작성해주세요</li>
                    <li>스크린샷이나 이미지를 포함하면 더 좋습니다</li>
                </ul>
            </div>

            <form id="review-submission-form">
                <div class="form-group">
                    <label for="blog_url">블로그 후기 URL</label>
                    <input type="url"
                           id="blog_url"
                           name="blog_url"
                           class="form-control"
                           placeholder="https://blog.naver.com/myid/123456789"
                           required>
                    <small class="form-text">후기를 작성한 블로그 게시물의 URL을 입력하세요</small>
                </div>

                <div class="form-group">
                    <label for="review_title">후기 제목</label>
                    <input type="text"
                           id="review_title"
                           name="review_title"
                           class="form-control"
                           placeholder="돈린이 100원 전자책 솔직 후기"
                           required>
                </div>

                <div class="form-group">
                    <label for="review_summary">후기 요약 (선택)</label>
                    <textarea id="review_summary"
                              name="review_summary"
                              class="form-control"
                              rows="3"
                              placeholder="후기의 핵심 내용을 간단히 요약해주세요"></textarea>
                </div>

                <div class="form-check">
                    <input type="checkbox"
                           id="agree_terms"
                           name="agree_terms"
                           class="form-check-input"
                           required>
                    <label for="agree_terms" class="form-check-label">
                        작성한 후기가 실제이며, 검수 과정에서 확인될 수 있음에 동의합니다.
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-lg">
                    후기 제출하기
                </button>
            </form>

            <div class="review-notice">
                <p>⏱ 검수는 통상 24시간 이내에 완료됩니다.</p>
                <p>✅ 승인되면 즉시 18만원 할인 쿠폰이 발급됩니다.</p>
            </div>
        </div>

        <style>
        .review-submission-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
        }
        .progress-status {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            position: relative;
        }
        .progress-status::before {
            content: '';
            position: absolute;
            top: 25px;
            left: 10%;
            right: 10%;
            height: 2px;
            background: #ddd;
            z-index: -1;
        }
        .status-item {
            text-align: center;
            flex: 1;
        }
        .status-icon {
            display: inline-block;
            width: 50px;
            height: 50px;
            line-height: 50px;
            border-radius: 50%;
            background: #ddd;
            color: white;
            font-size: 20px;
            font-weight: bold;
        }
        .status-item.completed .status-icon {
            background: #27ae60;
        }
        .status-item.active .status-icon {
            background: #3498db;
        }
        .status-text {
            display: block;
            margin-top: 10px;
            font-size: 14px;
        }
        .review-guidelines {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
        }
        .review-guidelines h3 {
            margin-top: 0;
        }
        .review-guidelines ul {
            margin-bottom: 0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-text {
            display: block;
            margin-top: 5px;
            color: #6c757d;
            font-size: 14px;
        }
        .form-check {
            margin: 20px 0;
        }
        .form-check-input {
            margin-right: 10px;
        }
        .btn-primary {
            background: #3498db;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            width: 100%;
        }
        .btn-primary:hover {
            background: #2980b9;
        }
        .btn-primary:disabled {
            background: #95a5a6;
            cursor: not-allowed;
        }
        .review-notice {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .review-notice p {
            margin: 5px 0;
        }
        .alert {
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        </style>

        <script>
        jQuery(document).ready(function($) {
            $('#review-submission-form').on('submit', function(e) {
                e.preventDefault();

                var $form = $(this);
                var $button = $form.find('button[type="submit"]');

                // Validate URL format
                var url = $('#blog_url').val();
                if (!isValidUrl(url)) {
                    alert('올바른 URL을 입력해주세요.');
                    return;
                }

                // Disable button
                $button.prop('disabled', true).text('제출 중...');

                // Submit review
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'donlinee_submit_review',
                        blog_url: $('#blog_url').val(),
                        review_title: $('#review_title').val(),
                        review_summary: $('#review_summary').val(),
                        nonce: '<?php echo wp_create_nonce('donlinee-review-nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('후기가 성공적으로 제출되었습니다!');
                            location.reload();
                        } else {
                            alert(response.data || '제출 중 오류가 발생했습니다.');
                            $button.prop('disabled', false).text('후기 제출하기');
                        }
                    },
                    error: function() {
                        alert('서버 오류가 발생했습니다.');
                        $button.prop('disabled', false).text('후기 제출하기');
                    }
                });
            });

            function isValidUrl(string) {
                try {
                    new URL(string);
                    return true;
                } catch (_) {
                    return false;
                }
            }
        });
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * Submit review AJAX handler
     */
    public function submit_review() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'donlinee-review-nonce')) {
            wp_send_json_error('보안 검증 실패');
        }

        if (!is_user_logged_in()) {
            wp_send_json_error('로그인이 필요합니다.');
        }

        $user_id = get_current_user_id();
        $blog_url = esc_url_raw($_POST['blog_url']);
        $review_title = sanitize_text_field($_POST['review_title']);
        $review_summary = sanitize_textarea_field($_POST['review_summary']);

        // Validate URL
        if (!filter_var($blog_url, FILTER_VALIDATE_URL)) {
            wp_send_json_error('올바른 URL을 입력해주세요.');
        }

        // Check if user has completed the ebook
        $progress = $this->get_user_ebook_progress($user_id);
        if (!$progress || $progress->progress_percentage < 100) {
            wp_send_json_error('전자책을 먼저 완독해주세요.');
        }

        // Check for duplicate submission
        $existing = $this->get_user_review($user_id);
        if ($existing) {
            wp_send_json_error('이미 후기를 제출하셨습니다.');
        }

        // Get ebook ID
        $ebook_id = $this->get_user_ebook_id($user_id);

        // Save review submission
        global $wpdb;
        $table = $wpdb->prefix . 'donlinee_ebook_reviews';

        $result = $wpdb->insert($table, array(
            'user_id' => $user_id,
            'ebook_id' => $ebook_id,
            'blog_url' => $blog_url,
            'reviewer_notes' => json_encode(array(
                'title' => $review_title,
                'summary' => $review_summary
            )),
            'status' => 'pending'
        ));

        if ($result) {
            // Send notification to admin
            $this->notify_admin_new_review($user_id, $blog_url);

            // Send AlimTalk to user
            do_action('donlinee_review_submitted', $user_id);

            wp_send_json_success('후기가 제출되었습니다. 검수 후 쿠폰이 발급됩니다.');
        } else {
            wp_send_json_error('후기 제출 실패');
        }
    }

    /**
     * Validate blog URL (check if content exists)
     */
    public function validate_blog_url() {
        $url = esc_url_raw($_POST['url']);

        // Basic validation
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            wp_send_json_error('Invalid URL');
        }

        // Try to fetch the content
        $response = wp_remote_get($url, array(
            'timeout' => 10,
            'user-agent' => 'Mozilla/5.0 (compatible; DonlineeBot/1.0)'
        ));

        if (is_wp_error($response)) {
            wp_send_json_error('URL에 접근할 수 없습니다.');
        }

        $body = wp_remote_retrieve_body($response);

        // Check if content contains required keywords
        $required_keywords = array('돈린이', '100원', '전자책');
        $found_keywords = 0;

        foreach ($required_keywords as $keyword) {
            if (mb_stripos($body, $keyword) !== false) {
                $found_keywords++;
            }
        }

        if ($found_keywords >= 2) {
            wp_send_json_success('유효한 후기입니다.');
        } else {
            wp_send_json_error('후기에 필수 키워드가 포함되지 않았습니다.');
        }
    }

    /**
     * Render review status
     */
    private function render_review_status($review) {
        ob_start();
        ?>
        <div class="review-status-container">
            <h2>후기 제출 상태</h2>

            <div class="status-card status-<?php echo esc_attr($review->status); ?>">
                <div class="status-header">
                    <?php if ($review->status === 'pending') : ?>
                        <span class="status-icon">⏳</span>
                        <h3>검수 중</h3>
                    <?php elseif ($review->status === 'approved') : ?>
                        <span class="status-icon">✅</span>
                        <h3>승인 완료</h3>
                    <?php else : ?>
                        <span class="status-icon">❌</span>
                        <h3>반려</h3>
                    <?php endif; ?>
                </div>

                <div class="review-info">
                    <p><strong>제출일:</strong> <?php echo date('Y년 m월 d일', strtotime($review->created_at)); ?></p>
                    <p><strong>블로그 URL:</strong> <a href="<?php echo esc_url($review->blog_url); ?>" target="_blank"><?php echo esc_html($review->blog_url); ?></a></p>

                    <?php if ($review->status === 'approved') : ?>
                        <div class="coupon-info">
                            <h4>🎉 18만원 할인 쿠폰이 발급되었습니다!</h4>
                            <?php
                            $coupon = $this->get_user_coupon($review->user_id);
                            if ($coupon) : ?>
                                <div class="coupon-code">
                                    <span>쿠폰 코드:</span>
                                    <strong><?php echo esc_html($coupon->coupon_code); ?></strong>
                                </div>
                                <p>유효기간: <?php echo date('Y년 m월 d일', strtotime($coupon->expires_at)); ?>까지</p>
                                <a href="<?php echo home_url('/checkout'); ?>" class="btn btn-success">쿠폰 사용하러 가기</a>
                            <?php endif; ?>
                        </div>
                    <?php elseif ($review->status === 'rejected' && $review->admin_notes) : ?>
                        <div class="rejection-reason">
                            <h4>반려 사유</h4>
                            <p><?php echo esc_html($review->admin_notes); ?></p>
                            <button class="btn btn-primary" onclick="location.reload()">다시 제출하기</button>
                        </div>
                    <?php else : ?>
                        <p class="pending-notice">검수는 통상 24시간 이내에 완료됩니다.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <style>
        .review-status-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .status-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .status-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .status-icon {
            font-size: 48px;
            display: block;
            margin-bottom: 10px;
        }
        .status-pending {
            border-top: 5px solid #f39c12;
        }
        .status-approved {
            border-top: 5px solid #27ae60;
        }
        .status-rejected {
            border-top: 5px solid #e74c3c;
        }
        .coupon-info {
            background: #d4edda;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .coupon-code {
            background: white;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin: 15px 0;
            font-size: 24px;
        }
        .rejection-reason {
            background: #f8d7da;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .btn-success {
            background: #27ae60;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        </style>
        <?php
        return ob_get_clean();
    }

    /**
     * Get user's ebook progress
     */
    private function get_user_ebook_progress($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'donlinee_ebook_progress';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d ORDER BY updated_at DESC LIMIT 1",
            $user_id
        ));
    }

    /**
     * Get user's review
     */
    private function get_user_review($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'donlinee_ebook_reviews';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d ORDER BY created_at DESC LIMIT 1",
            $user_id
        ));
    }

    /**
     * Get user's ebook ID
     */
    private function get_user_ebook_id($user_id) {
        // For now, return the first ebook
        // In production, this would check user's purchases
        $ebooks = get_posts(array(
            'post_type' => 'donlinee_ebook',
            'numberposts' => 1
        ));

        return $ebooks ? $ebooks[0]->ID : 1;
    }

    /**
     * Get user's coupon
     */
    private function get_user_coupon($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'donlinee_generated_coupons';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d AND review_approved = 1 ORDER BY generated_at DESC LIMIT 1",
            $user_id
        ));
    }

    /**
     * Notify admin of new review submission
     */
    private function notify_admin_new_review($user_id, $blog_url) {
        $user = get_userdata($user_id);
        $admin_email = get_option('admin_email');

        $subject = '[돈린이] 새로운 후기가 제출되었습니다';
        $message = sprintf(
            "새로운 후기가 제출되어 검수가 필요합니다.\n\n" .
            "사용자: %s (%s)\n" .
            "블로그 URL: %s\n\n" .
            "관리자 페이지에서 검수해주세요:\n%s",
            $user->display_name,
            $user->user_email,
            $blog_url,
            admin_url('admin.php?page=donlinee-ebook-reviews')
        );

        wp_mail($admin_email, $subject, $message);
    }

    /**
     * Approve review (Admin)
     */
    public function approve_review() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('권한이 없습니다.');
        }

        $review_id = intval($_POST['review_id']);

        // Update review status
        global $wpdb;
        $table = $wpdb->prefix . 'donlinee_ebook_reviews';

        $review = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $review_id
        ));

        if (!$review) {
            wp_send_json_error('리뷰를 찾을 수 없습니다.');
        }

        $wpdb->update(
            $table,
            array(
                'status' => 'approved',
                'reviewed_by' => get_current_user_id(),
                'reviewed_at' => current_time('mysql')
            ),
            array('id' => $review_id)
        );

        // Trigger coupon generation
        do_action('donlinee_review_approved', $review);

        wp_send_json_success('후기가 승인되었습니다.');
    }

    /**
     * Reject review (Admin)
     */
    public function reject_review() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('권한이 없습니다.');
        }

        $review_id = intval($_POST['review_id']);
        $reason = sanitize_textarea_field($_POST['reason']);

        global $wpdb;
        $table = $wpdb->prefix . 'donlinee_ebook_reviews';

        $wpdb->update(
            $table,
            array(
                'status' => 'rejected',
                'admin_notes' => $reason,
                'reviewed_by' => get_current_user_id(),
                'reviewed_at' => current_time('mysql')
            ),
            array('id' => $review_id)
        );

        wp_send_json_success('후기가 반려되었습니다.');
    }
}