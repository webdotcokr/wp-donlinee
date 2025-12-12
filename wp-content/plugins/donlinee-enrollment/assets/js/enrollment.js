jQuery(document).ready(function($) {
    let currentEnrollmentId = null;

    // 모드에 따라 트리거 클래스 및 텍스트 동적 변경
    function updateTriggerButtons() {
        const currentMode = donlinee_enrollment.current_mode;
        const buttonText = currentMode === 'enrollment' ?
                          donlinee_enrollment.enrollment_button_text :
                          donlinee_enrollment.waitlist_button_text;

        // 모든 CTA 버튼 업데이트
        $('.donlinee-waitlist-trigger, .donlinee-enrollment-trigger').each(function() {
            const $btn = $(this);

            if (currentMode === 'enrollment' && donlinee_enrollment.is_active) {
                $btn.removeClass('donlinee-waitlist-trigger')
                    .addClass('donlinee-enrollment-trigger');
            } else {
                $btn.removeClass('donlinee-enrollment-trigger')
                    .addClass('donlinee-waitlist-trigger');
            }

            // 텍스트 업데이트
            $btn.find('span').text(buttonText);
        });

        // 카운트다운 텍스트 업데이트
        updateCountdownText();
    }

    // 카운트다운 텍스트 업데이트
    function updateCountdownText() {
        const currentMode = donlinee_enrollment.current_mode;
        const countdownText = currentMode === 'enrollment' ?
                             donlinee_enrollment.countdown_text_enrollment :
                             donlinee_enrollment.countdown_text_waitlist;

        // footer.php의 카운트다운 레이블 업데이트
        const $countdownLabel = $('#countdown-timer').prev('.text-xs.text-gray-300');
        if ($countdownLabel.length) {
            $countdownLabel.text(countdownText);
        }
    }

    // 초기화
    updateTriggerButtons();

    // MutationObserver로 동적으로 추가되는 버튼도 감지
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                setTimeout(updateTriggerButtons, 100);
            }
        });
    });

    // body 전체 감시
    observer.observe(document.body, { childList: true, subtree: true });

    // 수강 신청 버튼 클릭 이벤트
    $(document).on('click', '.donlinee-enrollment-trigger', function(e) {
        e.preventDefault();
        e.stopPropagation(); // 이벤트 버블링 방지

        // 기존 waitlist 팝업이 열려있으면 닫기
        $('#donlinee-waitlist-popup').hide();

        // enrollment 팝업만 열기
        $('#donlinee-enrollment-popup').fadeIn(300);
        $('body').css('overflow', 'hidden');
    });

    // 팝업 닫기
    $('.donlinee-popup-close, .donlinee-confirm-btn').on('click', function() {
        $('#donlinee-enrollment-popup').fadeOut(300);
        $('body').css('overflow', 'auto');
        resetForm();
    });

    // ESC 키로 팝업 닫기
    $(document).keydown(function(e) {
        if (e.keyCode === 27) {
            $('#donlinee-enrollment-popup').fadeOut(300);
            $('body').css('overflow', 'auto');
            resetForm();
        }
    });

    // 오버레이 클릭으로 팝업 닫기
    $('#donlinee-enrollment-popup').on('click', function(e) {
        if ($(e.target).is('#donlinee-enrollment-popup')) {
            $(this).fadeOut(300);
            $('body').css('overflow', 'auto');
            resetForm();
        }
    });

    // 폼 제출 처리
    $('#donlinee-enrollment-form').on('submit', function(e) {
        e.preventDefault();

        // 유효성 검사
        if (!validateForm()) {
            return false;
        }

        const $submitBtn = $(this).find('.donlinee-submit-btn');
        const originalText = $submitBtn.text();

        // 버튼 비활성화
        $submitBtn.prop('disabled', true)
                  .html('<span class="loading-spinner"></span> 처리중...');

        // 에러 메시지 초기화
        $('.error-message').removeClass('show');

        // AJAX 요청
        $.ajax({
            url: donlinee_enrollment.ajax_url,
            type: 'POST',
            data: {
                action: 'donlinee_submit_enrollment',
                nonce: donlinee_enrollment.nonce,
                name: $('#enrollment-name').val(),
                age_gender: $('#enrollment-age-gender').val(),
                phone: $('#enrollment-phone').val(),
                self_intro: $('#enrollment-self-intro').val(),
                sales_experience: $('#enrollment-sales-exp').val(),
                application_reason: $('#enrollment-reason').val(),
                future_plans: $('#enrollment-future').val(),
                refund_account: $('#enrollment-refund').val()
            },
            success: function(response) {
                if (response.success) {
                    // 신청 ID 저장
                    currentEnrollmentId = response.data.id;

                    // 이름 표시
                    $('#applicant-name').text(response.data.name);

                    // 결제 방법 선택 단계로 이동
                    $('#enrollment-form-step').fadeOut(300, function() {
                        $('#payment-method-step').fadeIn(300);
                    });
                } else {
                    // 에러 처리
                    if (response.data.field) {
                        $('#' + response.data.field + '-error').text(response.data.message).addClass('show');
                    } else {
                        alert(response.data.message);
                    }
                }
            },
            error: function() {
                alert('신청 처리 중 오류가 발생했습니다. 다시 시도해주세요.');
            },
            complete: function() {
                // 버튼 활성화
                $submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    // 결제 방법 선택 - 계좌이체
    $('#select-transfer').on('click', function() {
        if (!currentEnrollmentId) {
            alert('신청 정보가 없습니다. 다시 시도해주세요.');
            return;
        }

        const $btn = $(this);
        $btn.prop('disabled', true).text('처리중...');

        $.ajax({
            url: donlinee_enrollment.ajax_url,
            type: 'POST',
            data: {
                action: 'donlinee_update_payment_method',
                nonce: donlinee_enrollment.nonce,
                id: currentEnrollmentId,
                payment_method: 'transfer'
            },
            success: function(response) {
                if (response.success) {
                    // 계좌이체 안내 화면으로 이동
                    $('#payment-method-step').fadeOut(300, function() {
                        $('#payment-complete-title').text('계좌이체 안내');
                        $('#transfer-instructions').show();
                        $('#card-instructions').hide();
                        $('#payment-complete-step').fadeIn(300);
                    });
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert('처리 중 오류가 발생했습니다.');
            },
            complete: function() {
                $btn.prop('disabled', false).text('계좌이체로 결제하기');
            }
        });
    });

    // 결제 방법 선택 - 카드결제
    $('#select-card').on('click', function() {
        if (!currentEnrollmentId) {
            alert('신청 정보가 없습니다. 다시 시도해주세요.');
            return;
        }

        const $btn = $(this);
        $btn.prop('disabled', true).text('처리중...');

        $.ajax({
            url: donlinee_enrollment.ajax_url,
            type: 'POST',
            data: {
                action: 'donlinee_update_payment_method',
                nonce: donlinee_enrollment.nonce,
                id: currentEnrollmentId,
                payment_method: 'card'
            },
            success: function(response) {
                if (response.success) {
                    // 카드 결제 페이지 새창 열기
                    window.open(donlinee_enrollment.payment_url, '_blank');

                    // 카드결제 안내 화면으로 이동
                    $('#payment-method-step').fadeOut(300, function() {
                        $('#payment-complete-title').text('카드결제 안내');
                        $('#transfer-instructions').hide();
                        $('#card-instructions').show();
                        $('#payment-complete-step').fadeIn(300);
                    });
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert('처리 중 오류가 발생했습니다.');
            },
            complete: function() {
                $btn.prop('disabled', false).text('카드로 결제하기');
            }
        });
    });

    // 카드 결제 재시도
    $('#retry-card-payment').on('click', function() {
        window.open(donlinee_enrollment.payment_url, '_blank');
    });

    // 이전 단계로 돌아가기
    $('#back-to-form').on('click', function() {
        $('#payment-method-step').fadeOut(300, function() {
            $('#enrollment-form-step').fadeIn(300);
        });
    });

    // 팝업 닫기 (최종)
    $('#close-enrollment-popup').on('click', function() {
        $('#donlinee-enrollment-popup').fadeOut(300);
        $('body').css('overflow', 'auto');
        resetForm();
    });

    // 폼 유효성 검사
    function validateForm() {
        let isValid = true;
        $('.error-message').removeClass('show');

        // 이름 검사
        const name = $('#enrollment-name').val().trim();
        if (!name) {
            $('#name-error').text('이름을 입력해주세요.').addClass('show');
            isValid = false;
        }

        // 나이/성별 검사
        const ageGender = $('#enrollment-age-gender').val().trim();
        if (!ageGender) {
            $('#age-gender-error').text('나이와 성별을 입력해주세요.').addClass('show');
            isValid = false;
        }

        // 전화번호 검사
        const phone = $('#enrollment-phone').val().trim();
        const phoneRegex = /^01[0-9]-?[0-9]{3,4}-?[0-9]{4}$/;
        if (!phone) {
            $('#phone-error').text('연락처를 입력해주세요.').addClass('show');
            isValid = false;
        } else if (!phoneRegex.test(phone.replace(/-/g, ''))) {
            $('#phone-error').text('올바른 전화번호 형식이 아닙니다.').addClass('show');
            isValid = false;
        }

        // 자기소개 검사
        const selfIntro = $('#enrollment-self-intro').val().trim();
        if (!selfIntro) {
            $('#self-intro-error').text('자기소개를 입력해주세요.').addClass('show');
            isValid = false;
        }

        // 판매 경험 검사
        const salesExp = $('#enrollment-sales-exp').val().trim();
        if (!salesExp) {
            $('#sales-exp-error').text('판매 경험을 입력해주세요. (없으면 "없음"이라고 작성)').addClass('show');
            isValid = false;
        }

        // 지원 이유 검사
        const reason = $('#enrollment-reason').val().trim();
        if (!reason) {
            $('#reason-error').text('지원 이유를 입력해주세요.').addClass('show');
            isValid = false;
        }

        // 환불 계좌 검사
        const refund = $('#enrollment-refund').val().trim();
        if (!refund) {
            $('#refund-error').text('환불 계좌를 입력해주세요.').addClass('show');
            isValid = false;
        }

        return isValid;
    }

    // 폼 초기화
    function resetForm() {
        $('#donlinee-enrollment-form')[0].reset();
        $('.error-message').removeClass('show');
        currentEnrollmentId = null;

        // 모든 스텝 초기화
        $('#enrollment-form-step').show();
        $('#payment-method-step').hide();
        $('#payment-complete-step').hide();
    }

    // 전화번호 자동 포맷팅
    $('#enrollment-phone').on('input', function() {
        let value = $(this).val().replace(/[^0-9]/g, '');
        let formattedValue = '';

        if (value.length <= 3) {
            formattedValue = value;
        } else if (value.length <= 7) {
            formattedValue = value.slice(0, 3) + '-' + value.slice(3);
        } else if (value.length <= 11) {
            formattedValue = value.slice(0, 3) + '-' + value.slice(3, 7) + '-' + value.slice(7);
        }

        $(this).val(formattedValue);
    });
});