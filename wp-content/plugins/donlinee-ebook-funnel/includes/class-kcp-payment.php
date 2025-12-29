<?php
/**
 * NHN KCP Payment Gateway Integration Class
 *
 * @package DonlineeEbookFunnel
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class DonlineeKCPPayment {

    private static $instance = null;

    // KCP 테스트 정보
    private $test_mode = true;
    private $site_cd = 'T0000'; // 테스트 사이트 코드
    private $site_key = '3grptw1.zW0GSo4PQdaGvsF__'; // 테스트 사이트 키
    private $pay_method = 'CARD'; // 결제 수단

    // KCP URLs
    private $kcp_api_url;
    private $kcp_js_url;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Set KCP URLs based on mode
        if ($this->test_mode) {
            $this->kcp_api_url = 'https://stg-spl.kcp.co.kr/gw/enc/v1/payment';
            $this->kcp_js_url = 'https://testpay.kcp.co.kr/plugin/payplus_web.jsp';
        } else {
            $this->kcp_api_url = 'https://spl.kcp.co.kr/gw/enc/v1/payment';
            $this->kcp_js_url = 'https://pay.kcp.co.kr/plugin/payplus_web.jsp';
        }

        // Initialize hooks
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        // AJAX handlers
        add_action('wp_ajax_kcp_process_payment', array($this, 'process_payment'));
        add_action('wp_ajax_nopriv_kcp_process_payment', array($this, 'process_payment'));
        add_action('wp_ajax_kcp_payment_approval', array($this, 'payment_approval'));
        add_action('wp_ajax_nopriv_kcp_payment_approval', array($this, 'payment_approval'));

        // Register payment result endpoints
        add_action('init', array($this, 'register_endpoints'));
    }

    /**
     * Register endpoints for payment callbacks
     */
    public function register_endpoints() {
        add_rewrite_rule('^kcp-payment-result/?', 'index.php?kcp_payment_result=1', 'top');
        add_rewrite_tag('%kcp_payment_result%', '1');

        add_action('template_redirect', array($this, 'handle_payment_result'));
    }

    /**
     * Enqueue KCP scripts
     */
    public function enqueue_scripts() {
        if (!is_page('ebook-checkout') && !has_shortcode(get_post()->post_content ?? '', 'donlinee_checkout')) {
            return;
        }

        // KCP Payment Script
        wp_enqueue_script(
            'kcp-payment-script',
            $this->kcp_js_url,
            array(),
            null,
            true
        );

        // Custom KCP handler
        wp_enqueue_script(
            'donlinee-kcp-payment',
            DONLINEE_EBOOK_PLUGIN_URL . 'assets/js/kcp-payment.js',
            array('jquery'),
            DONLINEE_EBOOK_VERSION,
            true
        );

        wp_localize_script('donlinee-kcp-payment', 'kcp_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kcp-payment-nonce'),
            'site_cd' => $this->site_cd,
            'site_key' => $this->site_key,
            'test_mode' => $this->test_mode,
            'return_url' => home_url('/kcp-payment-result'),
            'is_mobile' => wp_is_mobile()
        ));
    }

    /**
     * Process payment request
     */
    public function process_payment() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'kcp-payment-nonce')) {
            wp_send_json_error('보안 검증 실패');
        }

        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error('로그인이 필요합니다.');
        }

        $user = wp_get_current_user();
        $product_id = intval($_POST['product_id']);
        $amount = intval($_POST['amount']);

        // Generate unique order number
        $order_no = 'EBOOK_' . date('YmdHis') . '_' . $user->ID;

        // Prepare payment data for KCP
        $payment_data = array(
            'ordr_idxx' => $order_no,                    // 주문번호
            'good_name' => '100원 재테크 전자책',         // 상품명
            'good_mny' => $amount,                        // 결제금액
            'buyr_name' => $user->display_name,          // 구매자명
            'buyr_mail' => $user->user_email,            // 구매자 이메일
            'buyr_tel1' => get_user_meta($user->ID, 'billing_phone', true) ?: '01012345678', // 구매자 전화번호
            'pay_method' => $this->pay_method,           // 결제수단
            'currency' => 'WON',                         // 통화
            'skin_indx' => 1,                            // 스킨 인덱스
            'site_cd' => $this->site_cd,                 // 사이트 코드
            'site_name' => get_bloginfo('name'),         // 사이트명
            'eng_flag' => 'N',                           // 영문 사용 여부
            'res_cd' => '',                              // 결과 코드
            'res_msg' => '',                             // 결과 메시지
            'enc_info' => '',                            // 암호화 정보
            'enc_data' => '',                            // 암호화 데이터
            'tran_cd' => '',                            // 트랜잭션 코드
            'use_pay_method' => '100000000000',         // 신용카드만 사용
            'cash_yn' => 'N',                           // 현금영수증 사용여부

            // 빌링키 발급을 위한 정보 (정기결제)
            'pay_type' => 'BATCH',                      // BATCH: 빌링키 발급
            'fix_type' => 'CARD',                       // 카드 고정
        );

        // Save order data to session or database temporarily
        set_transient('kcp_order_' . $order_no, array(
            'user_id' => $user->ID,
            'product_id' => $product_id,
            'amount' => $amount,
            'order_no' => $order_no
        ), 3600); // 1 hour expiry

        wp_send_json_success(array(
            'payment_data' => $payment_data,
            'order_no' => $order_no
        ));
    }

    /**
     * Handle payment approval
     */
    public function payment_approval() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'kcp-payment-nonce')) {
            wp_send_json_error('보안 검증 실패');
        }

        $enc_info = sanitize_text_field($_POST['enc_info']);
        $enc_data = sanitize_text_field($_POST['enc_data']);
        $ordr_idxx = sanitize_text_field($_POST['ordr_idxx']);
        $res_cd = sanitize_text_field($_POST['res_cd']);

        // Check if payment was successful
        if ($res_cd !== '0000') {
            wp_send_json_error('결제 실패: ' . $this->get_error_message($res_cd));
        }

        // Get order data
        $order_data = get_transient('kcp_order_' . $ordr_idxx);

        if (!$order_data) {
            wp_send_json_error('주문 정보를 찾을 수 없습니다.');
        }

        // Process the approval with KCP
        $approval_result = $this->send_approval_request($enc_info, $enc_data, $ordr_idxx);

        if ($approval_result['success']) {
            // Save payment record
            $this->save_payment_record($approval_result['data'], $order_data);

            // Create WooCommerce order if available
            if (class_exists('WooCommerce')) {
                $this->create_woocommerce_order($approval_result['data'], $order_data);
            }

            // Send AlimTalk notification
            do_action('donlinee_after_ebook_purchase', array(
                'user_id' => $order_data['user_id'],
                'product_name' => '100원 재테크 전자책',
                'amount' => $order_data['amount'],
                'product_id' => $order_data['product_id']
            ));

            // Clean up transient
            delete_transient('kcp_order_' . $ordr_idxx);

            wp_send_json_success(array(
                'message' => '결제가 완료되었습니다.',
                'redirect_url' => home_url('/payment-complete?order=' . $ordr_idxx)
            ));
        } else {
            wp_send_json_error('결제 승인 실패: ' . $approval_result['message']);
        }
    }

    /**
     * Send approval request to KCP
     */
    private function send_approval_request($enc_info, $enc_data, $ordr_idxx) {
        // KCP approval API implementation
        // This would contain the actual API call to KCP for payment approval

        // For testing, return success
        if ($this->test_mode) {
            return array(
                'success' => true,
                'data' => array(
                    'tno' => 'TEST' . time(),
                    'amount' => 100,
                    'card_name' => '테스트카드',
                    'card_no' => '1234********5678',
                    'app_time' => date('YmdHis'),
                    'app_no' => 'TEST123456'
                )
            );
        }

        // Production approval would go here
        return array('success' => false, 'message' => 'Not implemented');
    }

    /**
     * Save payment record
     */
    private function save_payment_record($payment_data, $order_data) {
        global $wpdb;

        $table = $wpdb->prefix . 'donlinee_payment_records';

        // Create table if not exists
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            order_id varchar(100) NOT NULL,
            tno varchar(100) NOT NULL,
            amount decimal(10,2) NOT NULL,
            status varchar(20) NOT NULL,
            payment_method varchar(50),
            card_name varchar(50),
            card_no varchar(20),
            app_time varchar(20),
            app_no varchar(50),
            paid_at datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY order_id (order_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Insert payment record
        $wpdb->insert($table, array(
            'user_id' => $order_data['user_id'],
            'order_id' => $order_data['order_no'],
            'tno' => $payment_data['tno'],
            'amount' => $payment_data['amount'],
            'status' => 'paid',
            'payment_method' => 'card',
            'card_name' => $payment_data['card_name'],
            'card_no' => $payment_data['card_no'],
            'app_time' => $payment_data['app_time'],
            'app_no' => $payment_data['app_no'],
            'paid_at' => current_time('mysql')
        ));

        return $wpdb->insert_id;
    }

    /**
     * Create WooCommerce order
     */
    private function create_woocommerce_order($payment_data, $order_data) {
        if (!class_exists('WC_Order')) {
            return null;
        }

        $order = wc_create_order();

        // Get or create ebook product
        $product_id = $this->get_or_create_ebook_product();

        if ($product_id) {
            $order->add_product(wc_get_product($product_id), 1);
        }

        // Set customer
        $order->set_customer_id($order_data['user_id']);

        // Set payment method
        $order->set_payment_method('kcp');
        $order->set_payment_method_title('KCP 신용카드');

        // Add order note
        $order->add_order_note('KCP 결제 완료 - TID: ' . $payment_data['tno']);

        // Set status to completed
        $order->update_status('completed');

        // Save
        $order->save();

        return $order;
    }

    /**
     * Get or create ebook product
     */
    private function get_or_create_ebook_product() {
        $product_id = get_option('donlinee_ebook_product_id');

        if (!$product_id && class_exists('WC_Product_Simple')) {
            $product = new WC_Product_Simple();
            $product->set_name('100원 재테크 전자책');
            $product->set_status('publish');
            $product->set_catalog_visibility('visible');
            $product->set_price(100);
            $product->set_regular_price(100);
            $product->set_sku('EBOOK100KCP');
            $product->set_manage_stock(false);
            $product->set_downloadable(true);
            $product->set_virtual(true);

            $product_id = $product->save();
            update_option('donlinee_ebook_product_id', $product_id);
        }

        return $product_id;
    }

    /**
     * Handle payment result page
     */
    public function handle_payment_result() {
        if (get_query_var('kcp_payment_result')) {
            // Process payment result
            include DONLINEE_EBOOK_PLUGIN_DIR . 'templates/kcp-payment-result.php';
            exit;
        }
    }

    /**
     * Get error message by code
     */
    private function get_error_message($code) {
        $messages = array(
            '8000' => '결제 취소',
            '8100' => '카드 인증 실패',
            '8110' => '카드 한도 초과',
            '8111' => '카드 정지',
            '8112' => '잘못된 카드 번호',
            '8120' => '유효기간 오류',
            '8130' => 'CVC 오류',
            '8140' => '할부 불가',
            '8150' => '카드사 점검중',
            '9999' => '기타 오류'
        );

        return isset($messages[$code]) ? $messages[$code] : '알 수 없는 오류 (' . $code . ')';
    }

    /**
     * Render KCP checkout form
     */
    public static function render_checkout_form() {
        if (!is_user_logged_in()) {
            return '<p>로그인이 필요합니다. <a href="#" onclick="donlineeKakaoLogin()">카카오로 로그인</a></p>';
        }

        ob_start();
        ?>
        <div class="kcp-checkout-container">
            <h2>100원 전자책 구매</h2>

            <div class="product-info">
                <h3>상품 정보</h3>
                <div class="product-item">
                    <span class="product-name">100원 재테크 가이드</span>
                    <span class="product-price">₩100</span>
                </div>
            </div>

            <div class="payment-method">
                <h3>결제 수단</h3>
                <div class="method-option selected">
                    <input type="radio" name="payment_method" value="card" checked>
                    <label>신용/체크카드</label>
                </div>
            </div>

            <div class="payment-notice">
                <p>📌 테스트 결제 안내</p>
                <ul>
                    <li>테스트 카드번호: 9410-0621-5254-5404</li>
                    <li>유효기간: 01/25</li>
                    <li>CVC: 123</li>
                    <li>카드 비밀번호: 00</li>
                </ul>
            </div>

            <button id="kcp-pay-button" class="btn-payment">
                100원 결제하기
            </button>

            <!-- KCP 결제창 Form -->
            <form name="kcp_pay_form" method="post" action="">
                <input type="hidden" name="ordr_idxx" value="">
                <input type="hidden" name="good_name" value="100원 재테크 전자책">
                <input type="hidden" name="good_mny" value="100">
                <input type="hidden" name="buyr_name" value="<?php echo esc_attr(wp_get_current_user()->display_name); ?>">
                <input type="hidden" name="buyr_mail" value="<?php echo esc_attr(wp_get_current_user()->user_email); ?>">
                <input type="hidden" name="buyr_tel1" value="01012345678">
                <input type="hidden" name="req_tx" value="pay">
                <input type="hidden" name="pay_method" value="100000000000">
                <input type="hidden" name="site_cd" value="<?php echo esc_attr(self::get_instance()->site_cd); ?>">
                <input type="hidden" name="site_name" value="<?php echo esc_attr(get_bloginfo('name')); ?>">
                <input type="hidden" name="res_cd" value="">
                <input type="hidden" name="res_msg" value="">
                <input type="hidden" name="enc_info" value="">
                <input type="hidden" name="enc_data" value="">
                <input type="hidden" name="ret_pay_method" value="">
                <input type="hidden" name="tran_cd" value="">
                <input type="hidden" name="use_pay_method" value="100000000000">
                <input type="hidden" name="cash_yn" value="N">
            </form>
        </div>

        <style>
        .kcp-checkout-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }
        .product-info, .payment-method, .payment-notice {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .product-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
        }
        .product-price {
            font-size: 24px;
            font-weight: bold;
            color: #2ecc71;
        }
        .method-option {
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
        }
        .method-option.selected {
            border-color: #3498db;
            background: #f0f8ff;
        }
        .payment-notice {
            background: #fff9e6;
            border-left: 4px solid #f39c12;
        }
        .payment-notice ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .btn-payment {
            width: 100%;
            padding: 20px;
            background: #3498db;
            color: white;
            font-size: 18px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-payment:hover {
            background: #2980b9;
        }
        .btn-payment:disabled {
            background: #95a5a6;
            cursor: not-allowed;
        }
        </style>
        <?php
        return ob_get_clean();
    }
}