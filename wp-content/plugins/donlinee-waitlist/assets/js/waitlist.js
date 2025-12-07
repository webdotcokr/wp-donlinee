(function($) {
    'use strict';

    $(document).ready(function() {

        // 팝업 열기 - 다양한 링크/버튼에서 작동
        function openWaitlistPopup(e) {
            e.preventDefault();
            $('#donlinee-waitlist-popup').fadeIn(300);
            $('body').css('overflow', 'hidden');
        }

        // 모든 대기 신청 링크에 이벤트 바인딩
        $(document).on('click', 'a[href="/contact"], a[href="/대기신청"], h2:contains("강의 대기 신청하기")', openWaitlistPopup);

        // h2 버튼 스타일의 경우 클릭 이벤트
        $(document).on('click', '.h2.button:contains("강의 대기 신청하기")', openWaitlistPopup);

        // 팝업 닫기
        function closeWaitlistPopup() {
            $('#donlinee-waitlist-popup').fadeOut(300);
            $('body').css('overflow', '');
            // 폼 리셋
            $('#donlinee-waitlist-form')[0].reset();
            $('.error-message').removeClass('show').text('');
            $('#donlinee-waitlist-form').show();
            $('#donlinee-success-message').hide();
        }

        // 닫기 버튼 클릭
        $('.donlinee-popup-close').on('click', closeWaitlistPopup);

        // 확인했습니다 버튼 클릭
        $('.donlinee-confirm-btn').on('click', closeWaitlistPopup);

        // 오버레이 클릭시 닫기
        $('#donlinee-waitlist-popup').on('click', function(e) {
            if (e.target === this) {
                closeWaitlistPopup();
            }
        });

        // ESC 키로 닫기
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $('#donlinee-waitlist-popup').is(':visible')) {
                closeWaitlistPopup();
            }
        });

        // 전화번호 자동 포맷팅
        $('#waitlist-phone').on('input', function() {
            let value = $(this).val().replace(/[^0-9]/g, '');
            let formattedValue = '';

            if (value.length <= 3) {
                formattedValue = value;
            } else if (value.length <= 7) {
                formattedValue = value.slice(0, 3) + '-' + value.slice(3);
            } else if (value.length <= 11) {
                formattedValue = value.slice(0, 3) + '-' + value.slice(3, 7) + '-' + value.slice(7);
            } else {
                formattedValue = value.slice(0, 3) + '-' + value.slice(3, 7) + '-' + value.slice(7, 11);
            }

            $(this).val(formattedValue);
        });

        // 폼 유효성 검사
        function validateForm() {
            let isValid = true;
            $('.error-message').removeClass('show').text('');

            // 이름 검사
            const name = $('#waitlist-name').val().trim();
            if (name.length < 2) {
                $('#name-error').addClass('show').text('이름을 2자 이상 입력해주세요.');
                $('#waitlist-name').addClass('error');
                isValid = false;
            } else {
                $('#waitlist-name').removeClass('error');
            }

            // 전화번호 검사
            const phone = $('#waitlist-phone').val().trim();
            const phoneRegex = /^01[0-9]-[0-9]{3,4}-[0-9]{4}$/;
            if (!phoneRegex.test(phone)) {
                $('#phone-error').addClass('show').text('올바른 전화번호 형식을 입력해주세요. (예: 010-1234-5678)');
                $('#waitlist-phone').addClass('error');
                isValid = false;
            } else {
                $('#waitlist-phone').removeClass('error');
            }

            return isValid;
        }

        // 입력 필드 변경시 에러 메시지 제거
        $('#waitlist-name, #waitlist-phone').on('input', function() {
            $(this).removeClass('error');
            $(this).siblings('.error-message').removeClass('show').text('');
        });

        // 폼 제출
        $('#donlinee-waitlist-form').on('submit', function(e) {
            e.preventDefault();

            if (!validateForm()) {
                return false;
            }

            const submitBtn = $('.donlinee-submit-btn');
            const originalText = submitBtn.text();

            // 버튼 비활성화 및 로딩 표시
            submitBtn.prop('disabled', true).html('<span class="donlinee-loading"></span>처리중...');

            const formData = {
                action: 'donlinee_waitlist_submit',
                nonce: donlinee_waitlist_ajax.nonce,
                name: $('#waitlist-name').val().trim(),
                phone: $('#waitlist-phone').val().trim()
            };

            $.ajax({
                url: donlinee_waitlist_ajax.ajax_url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        // 성공 메시지 표시
                        $('#success-name').text(formData.name);
                        $('#success-phone').text(formData.phone);
                        $('#donlinee-waitlist-form').hide();
                        $('#donlinee-success-message').fadeIn(300);
                    } else {
                        // 에러 메시지 표시
                        alert(response.data.message || '신청 중 오류가 발생했습니다. 다시 시도해주세요.');
                    }
                },
                error: function() {
                    alert('서버와의 통신 중 오류가 발생했습니다. 잠시 후 다시 시도해주세요.');
                },
                complete: function() {
                    // 버튼 원상복구
                    submitBtn.prop('disabled', false).text(originalText);
                }
            });
        });

    });

})(jQuery);