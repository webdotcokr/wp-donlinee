/**
 * KCP Payment JavaScript Handler
 */

(function($) {
    'use strict';

    var KCPPayment = {

        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            $('#kcp-pay-button').on('click', this.startPayment.bind(this));
        },

        startPayment: function(e) {
            e.preventDefault();

            var $button = $(e.target);
            $button.prop('disabled', true).text('처리 중...');

            // Request payment data from server
            $.ajax({
                url: kcp_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'kcp_process_payment',
                    product_id: 1, // Default ebook product
                    amount: 100,
                    nonce: kcp_params.nonce
                },
                success: function(response) {
                    if (response.success) {
                        KCPPayment.openPaymentWindow(response.data);
                    } else {
                        alert(response.data || '결제 처리 중 오류가 발생했습니다.');
                        $button.prop('disabled', false).text('100원 결제하기');
                    }
                },
                error: function() {
                    alert('서버 오류가 발생했습니다.');
                    $button.prop('disabled', false).text('100원 결제하기');
                }
            });
        },

        openPaymentWindow: function(data) {
            // KCP 결제창 호출
            if (kcp_params.test_mode) {
                console.log('KCP Test Payment Data:', data);

                // 테스트 모드에서는 직접 결제 시뮬레이션
                this.simulateTestPayment(data);
            } else {
                // 실제 KCP 결제창 호출
                this.callKCPPaymentWindow(data);
            }
        },

        simulateTestPayment: function(data) {
            // 테스트 결제 시뮬레이션
            var testModal = $('<div class="kcp-test-modal">' +
                '<div class="modal-content">' +
                '<h3>KCP 테스트 결제</h3>' +
                '<p>상품명: ' + data.payment_data.good_name + '</p>' +
                '<p>결제금액: ₩' + data.payment_data.good_mny + '</p>' +
                '<div class="test-card-info">' +
                '<h4>테스트 카드 정보 입력</h4>' +
                '<input type="text" id="test-card-number" placeholder="9410-0621-5254-5404" value="9410062152545404">' +
                '<input type="text" id="test-card-expiry" placeholder="MM/YY" value="01/25">' +
                '<input type="text" id="test-card-cvc" placeholder="123" value="123">' +
                '<input type="password" id="test-card-password" placeholder="00" value="00">' +
                '</div>' +
                '<div class="modal-buttons">' +
                '<button id="test-pay-confirm">결제하기</button>' +
                '<button id="test-pay-cancel">취소</button>' +
                '</div>' +
                '</div>' +
                '</div>');

            $('body').append(testModal);

            // 테스트 결제 승인
            $('#test-pay-confirm').on('click', function() {
                KCPPayment.processTestApproval(data);
                testModal.remove();
            });

            // 테스트 결제 취소
            $('#test-pay-cancel').on('click', function() {
                testModal.remove();
                $('#kcp-pay-button').prop('disabled', false).text('100원 결제하기');
            });
        },

        processTestApproval: function(data) {
            // 테스트 결제 승인 처리
            $.ajax({
                url: kcp_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'kcp_payment_approval',
                    ordr_idxx: data.order_no,
                    res_cd: '0000', // Success code
                    enc_info: 'TEST_ENC_INFO',
                    enc_data: 'TEST_ENC_DATA',
                    nonce: kcp_params.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // 결제 성공 - Facebook Pixel 추적
                        if (typeof fbq !== 'undefined') {
                            fbq('track', 'Purchase', {
                                value: 100,
                                currency: 'KRW',
                                content_type: 'product',
                                content_ids: ['ebook_100']
                            });
                        }

                        alert('결제가 완료되었습니다!');
                        window.location.href = response.data.redirect_url;
                    } else {
                        alert(response.data || '결제 승인 실패');
                        $('#kcp-pay-button').prop('disabled', false).text('100원 결제하기');
                    }
                },
                error: function() {
                    alert('결제 승인 중 오류가 발생했습니다.');
                    $('#kcp-pay-button').prop('disabled', false).text('100원 결제하기');
                }
            });
        },

        callKCPPaymentWindow: function(data) {
            // 실제 KCP 결제창 호출 (프로덕션용)
            var form = document.kcp_pay_form;

            // Form 데이터 설정
            form.ordr_idxx.value = data.order_no;
            form.good_name.value = data.payment_data.good_name;
            form.good_mny.value = data.payment_data.good_mny;
            form.buyr_name.value = data.payment_data.buyr_name;
            form.buyr_mail.value = data.payment_data.buyr_mail;
            form.buyr_tel1.value = data.payment_data.buyr_tel1;

            // KCP 결제창 호출
            if (typeof call_pay_form !== 'undefined') {
                call_pay_form();
            } else {
                console.error('KCP payment script not loaded');
                alert('결제 시스템 오류가 발생했습니다.');
                $('#kcp-pay-button').prop('disabled', false).text('100원 결제하기');
            }
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        KCPPayment.init();
    });

    // KCP 결제 결과 콜백
    window.kcp_payment_callback = function(rtndata) {
        console.log('KCP Payment Callback:', rtndata);

        if (rtndata.res_cd === '0000') {
            // 결제 성공 - 승인 요청
            $.ajax({
                url: kcp_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'kcp_payment_approval',
                    ordr_idxx: rtndata.ordr_idxx,
                    res_cd: rtndata.res_cd,
                    enc_info: rtndata.enc_info,
                    enc_data: rtndata.enc_data,
                    nonce: kcp_params.nonce
                },
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.data.redirect_url;
                    } else {
                        alert(response.data || '결제 처리 실패');
                    }
                }
            });
        } else {
            // 결제 실패
            alert('결제가 실패했습니다: ' + rtndata.res_msg);
            $('#kcp-pay-button').prop('disabled', false).text('100원 결제하기');
        }
    };

})(jQuery);

// Add test modal styles
(function() {
    var style = document.createElement('style');
    style.innerHTML = `
        .kcp-test-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        }
        .kcp-test-modal .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 400px;
            width: 90%;
        }
        .kcp-test-modal h3 {
            margin-top: 0;
            color: #333;
        }
        .kcp-test-modal .test-card-info {
            margin: 20px 0;
        }
        .kcp-test-modal input {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .kcp-test-modal .modal-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .kcp-test-modal button {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .kcp-test-modal #test-pay-confirm {
            background: #3498db;
            color: white;
        }
        .kcp-test-modal #test-pay-cancel {
            background: #95a5a6;
            color: white;
        }
    `;
    document.head.appendChild(style);
})();