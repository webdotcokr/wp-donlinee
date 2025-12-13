jQuery(document).ready(function($) {
    let currentEnrollmentId = null;
    let currentStep = 1;
    let formData = {};
    let popupLoaded = false;

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

    // MutationObserver 대신 이벤트 위임과 주기적 업데이트 사용
    // DOM이 변경될 수 있는 시점에만 업데이트
    $(document).on('ajaxComplete', updateTriggerButtons);

    // 페이지 로드 완료 후 한 번 더 업데이트
    $(window).on('load', function() {
        setTimeout(updateTriggerButtons, 500);
    });

    // 팝업 HTML을 동적으로 로드하는 함수
    function loadEnrollmentPopup() {
        if (popupLoaded) {
            return Promise.resolve();
        }

        return $.ajax({
            url: donlinee_enrollment.ajax_url,
            type: 'POST',
            data: {
                action: 'donlinee_load_enrollment_popup',
                nonce: donlinee_enrollment.nonce
            },
            success: function(response) {
                if (response.success) {
                    // placeholder를 실제 HTML로 교체
                    $('#enrollment-popup-placeholder').replaceWith(response.data.html);
                    popupLoaded = true;

                    // 팝업 이벤트 재바인딩
                    bindPopupEvents();
                }
            },
            error: function() {
                console.error('Failed to load enrollment popup');
            }
        });
    }

    // 동적으로 로드된 팝업의 이벤트 바인딩
    function bindPopupEvents() {
        // 이벤트 위임을 사용하므로 대부분의 이벤트는 이미 작동함
        // 필요한 경우 여기에 추가 바인딩
        currentStep = 1; // 스텝 초기화
        formData = {}; // 폼 데이터 초기화
    }

    // 단계 이동 함수
    function goToStep(step) {
        // 현재 단계 데이터 저장
        saveStepData(currentStep);

        // 모든 단계 숨기기
        $('.form-step').removeClass('active').hide();

        // 새 단계 표시
        $(`.form-step.step-${step}`).addClass('active').fadeIn(300);

        // 진행바 업데이트
        updateProgress(step);

        // 진행 단계 표시 업데이트
        $('.progress-step').removeClass('active completed');
        for (let i = 1; i < step; i++) {
            $(`.progress-step[data-step="${i}"]`).addClass('completed');
        }
        $(`.progress-step[data-step="${step}"]`).addClass('active');

        // Step 5일 때 리뷰 데이터 업데이트
        if (step === 5) {
            updateReviewData();
        }

        currentStep = step;

        // 스크롤 맨 위로
        $('.donlinee-popup-container').scrollTop(0);
    }

    // 진행바 업데이트
    function updateProgress(step) {
        const progressPercentage = (step / 5) * 100;
        $('#enrollment-progress-fill').css('width', progressPercentage + '%');
    }

    // 현재 단계 데이터 저장
    function saveStepData(step) {
        switch(step) {
            case 2:
                formData.name = $('#enrollment-name').val();
                formData.age_gender = $('#enrollment-age-gender').val();
                formData.phone = $('#enrollment-phone').val();
                formData.self_intro = $('#enrollment-self-intro').val();
                break;
            case 3:
                formData.sales_experience = $('#enrollment-sales-exp').val();
                formData.application_reason = $('#enrollment-reason').val();
                formData.future_plans = $('#enrollment-future').val();
                break;
            case 4:
                formData.refund_account = $('#enrollment-refund').val();
                break;
        }
        // sessionStorage에 저장
        sessionStorage.setItem('enrollmentFormData', JSON.stringify(formData));
    }

    // 저장된 데이터 불러오기
    function loadSavedData() {
        const savedData = sessionStorage.getItem('enrollmentFormData');
        if (savedData) {
            formData = JSON.parse(savedData);
            // 각 필드에 값 설정
            if (formData.name) $('#enrollment-name').val(formData.name);
            if (formData.age_gender) $('#enrollment-age-gender').val(formData.age_gender);
            if (formData.phone) $('#enrollment-phone').val(formData.phone);
            if (formData.self_intro) $('#enrollment-self-intro').val(formData.self_intro);
            if (formData.sales_experience) $('#enrollment-sales-exp').val(formData.sales_experience);
            if (formData.application_reason) $('#enrollment-reason').val(formData.application_reason);
            if (formData.future_plans) $('#enrollment-future').val(formData.future_plans);
            if (formData.refund_account) $('#enrollment-refund').val(formData.refund_account);
        }
    }

    // 리뷰 데이터 업데이트
    function updateReviewData() {
        saveStepData(currentStep); // 현재 단계 데이터 저장
        $('#review-name').text(formData.name || '-');
        $('#review-age-gender').text(formData.age_gender || '-');
        $('#review-phone').text(formData.phone || '-');
        $('#review-refund').text(formData.refund_account || '-');
    }

    // 단계별 유효성 검사
    function validateStep(step) {
        let isValid = true;
        $('.error-message').removeClass('show');

        switch(step) {
            case 2:
                // 이름 검사
                if (!$('#enrollment-name').val().trim()) {
                    $('#name-error').text('이름을 입력해주세요.').addClass('show');
                    isValid = false;
                }
                // 나이/성별 검사
                if (!$('#enrollment-age-gender').val().trim()) {
                    $('#age-gender-error').text('나이와 성별을 입력해주세요.').addClass('show');
                    isValid = false;
                }
                // 전화번호 검사 (형식 검사 제거 - 어떤 형식이든 허용)
                const phone = $('#enrollment-phone').val().trim();
                if (!phone) {
                    $('#phone-error').text('연락처를 입력해주세요.').addClass('show');
                    isValid = false;
                }
                // 형식 검사 제거 - 어떤 번호든 허용
                // 자기소개 검사
                if (!$('#enrollment-self-intro').val().trim()) {
                    $('#self-intro-error').text('자기소개를 입력해주세요.').addClass('show');
                    isValid = false;
                }
                break;
            case 3:
                // 판매 경험 검사
                if (!$('#enrollment-sales-exp').val().trim()) {
                    $('#sales-exp-error').text('판매 경험을 입력해주세요. (없으면 "없음"이라고 작성)').addClass('show');
                    isValid = false;
                }
                // 지원 이유 검사
                if (!$('#enrollment-reason').val().trim()) {
                    $('#reason-error').text('지원 이유를 입력해주세요.').addClass('show');
                    isValid = false;
                }
                break;
            case 4:
                // 환불 계좌 검사
                if (!$('#enrollment-refund').val().trim()) {
                    $('#refund-error').text('환불 계좌를 입력해주세요.').addClass('show');
                    isValid = false;
                }
                break;
        }

        return isValid;
    }

    // 다음 버튼 클릭 이벤트
    $(document).on('click', '.btn-next-step', function() {
        const nextStep = parseInt($(this).data('next'));

        // 현재 단계 유효성 검사
        if (currentStep > 1 && !validateStep(currentStep)) {
            return;
        }

        goToStep(nextStep);
    });

    // 이전 버튼 클릭 이벤트
    $(document).on('click', '.btn-prev-step', function() {
        const prevStep = parseInt($(this).data('prev'));
        goToStep(prevStep);
    });

    // 수강 신청 버튼 클릭 이벤트 (iOS 터치 대응)
    $(document).on('touchstart click', '.donlinee-enrollment-trigger', function(e) {
        // 이미 처리된 이벤트라면 무시 (중복 실행 방지)
        if (e.handled === true) return;
        e.handled = true;

        e.preventDefault();
        e.stopPropagation(); // 이벤트 버블링 방지

        // 기존 waitlist 팝업이 열려있으면 닫기
        $('#donlinee-waitlist-popup').hide();

        // 팝업이 로드되지 않았으면 먼저 로드
        if (!popupLoaded && window.shouldLoadPopup) {
            // 로딩 표시
            $('body').append('<div id="enrollment-loading" style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);z-index:999999;background:white;padding:20px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.2);">수강 신청 양식을 불러오는 중...</div>');

            loadEnrollmentPopup().then(function() {
                $('#enrollment-loading').remove();
                // enrollment 팝업 열기
                $('#donlinee-enrollment-popup').css({
                    'display': 'flex',
                    'opacity': 0
                }).animate({ opacity: 1 }, 300);
            });
        } else {
            // 이미 로드되었으면 바로 열기
            $('#donlinee-enrollment-popup').css({
                'display': 'flex',
                'opacity': 0
            }).animate({ opacity: 1 }, 300);
        }
        $('body').css('overflow', 'hidden');

        // 저장된 데이터 불러오기
        loadSavedData();

        // 저장된 단계가 있으면 해당 단계로, 없으면 첫 단계로
        const savedStep = sessionStorage.getItem('enrollmentCurrentStep');
        if (savedStep && parseInt(savedStep) > 1) {
            goToStep(parseInt(savedStep));
        } else {
            goToStep(1);
        }
    });

    // 팝업 닫기 (데이터 유지) - 동적 로딩된 요소에도 작동
    $(document).on('click', '.donlinee-popup-close', function() {
        saveStepData(currentStep); // 현재 단계 데이터 저장
        sessionStorage.setItem('enrollmentCurrentStep', currentStep); // 현재 단계 저장
        $('#donlinee-enrollment-popup').fadeOut(300);
        $('body').css('overflow', 'auto');
    });

    // 확인 버튼 클릭 시에만 초기화 - 동적 로딩된 요소에도 작동
    $(document).on('click', '.donlinee-confirm-btn', function() {
        $('#donlinee-enrollment-popup').fadeOut(300);
        $('body').css('overflow', 'auto');
        resetForm();
    });

    // ESC 키로 팝업 닫기 (데이터 유지)
    $(document).keydown(function(e) {
        if (e.keyCode === 27 && $('#donlinee-enrollment-popup').is(':visible')) {
            saveStepData(currentStep);
            sessionStorage.setItem('enrollmentCurrentStep', currentStep);
            $('#donlinee-enrollment-popup').fadeOut(300);
            $('body').css('overflow', 'auto');
        }
    });

    // 오버레이 클릭으로 팝업 닫기 (데이터 유지) - 동적 로딩된 요소에도 작동
    $(document).on('click', '#donlinee-enrollment-popup', function(e) {
        if ($(e.target).is('#donlinee-enrollment-popup')) {
            saveStepData(currentStep);
            sessionStorage.setItem('enrollmentCurrentStep', currentStep);
            $(this).fadeOut(300);
            $('body').css('overflow', 'auto');
        }
    });

    // 폼 제출 처리 (동적 로딩된 폼에도 작동하도록 이벤트 위임 사용)
    $(document).on('submit', '#donlinee-enrollment-form', function(e) {
        e.preventDefault();

        // Step 5에서 제출하는 경우 최종 데이터 저장
        saveStepData(currentStep);

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
                name: formData.name,
                age_gender: formData.age_gender,
                phone: formData.phone,
                self_intro: formData.self_intro,
                sales_experience: formData.sales_experience,
                application_reason: formData.application_reason,
                future_plans: formData.future_plans,
                refund_account: formData.refund_account
            },
            success: function(response) {
                if (response.success) {
                    // 신청 ID 저장
                    currentEnrollmentId = response.data.id;

                    // 이름 표시
                    $('#applicant-name').text(response.data.name);

                    // 성공 시에만 sessionStorage 클리어
                    sessionStorage.removeItem('enrollmentFormData');
                    sessionStorage.removeItem('enrollmentCurrentStep');

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

    // 결제 방법 선택 - 계좌이체 (동적 로딩된 요소에도 작동)
    $(document).on('click', '#select-transfer', function() {
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

    // 결제 방법 선택 - 카드결제 (동적 로딩된 요소에도 작동)
    $(document).on('click', '#select-card', function() {
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

    // 이전 단계로 돌아가기 (동적 로딩된 요소에도 작동)
    $(document).on('click', '#back-to-form', function() {
        $('#payment-method-step').fadeOut(300, function() {
            $('#enrollment-form-step').fadeIn(300);
        });
    });

    // 팝업 닫기 (최종) (동적 로딩된 요소에도 작동)
    $(document).on('click', '#close-enrollment-popup', function() {
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

        // 전화번호 검사 (형식 검사 제거 - 어떤 형식이든 허용)
        const phone = $('#enrollment-phone').val().trim();
        if (!phone) {
            $('#phone-error').text('연락처를 입력해주세요.').addClass('show');
            isValid = false;
        }
        // 형식 검사 제거 - 어떤 번호든 허용

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
        formData = {};
        currentStep = 1;

        // sessionStorage 클리어
        sessionStorage.removeItem('enrollmentFormData');

        // 모든 스텝 초기화
        $('#enrollment-form-step').show();
        $('#payment-method-step').hide();
        $('#payment-complete-step').hide();

        // 첫 단계로 되돌리기
        $('.form-step').removeClass('active').hide();
        $('.form-step.step-1').addClass('active').show();

        // 진행바 초기화
        updateProgress(1);
        $('.progress-step').removeClass('active completed');
        $('.progress-step[data-step="1"]').addClass('active');
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

    // 자동 저장 - 입력 필드
    $('#enrollment-name, #enrollment-age-gender, #enrollment-phone, #enrollment-refund').on('blur', function() {
        saveStepData(currentStep);
    });

    // 자동 저장 - textarea (1초 딜레이)
    let saveTimer;
    $('#enrollment-self-intro, #enrollment-sales-exp, #enrollment-reason, #enrollment-future').on('input', function() {
        clearTimeout(saveTimer);
        saveTimer = setTimeout(() => {
            saveStepData(currentStep);
        }, 1000);
    });
});