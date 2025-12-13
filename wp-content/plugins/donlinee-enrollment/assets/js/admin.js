jQuery(document).ready(function($) {

    // 설정 폼 제출
    $('#enrollment-settings-form').on('submit', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $spinner = $form.find('.spinner');
        const $saveMessage = $form.find('.save-message');

        // 스피너 표시
        $spinner.css('display', 'inline-block');

        $.ajax({
            url: donlinee_enrollment_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'donlinee_save_settings',
                nonce: donlinee_enrollment_admin.nonce,
                mode: $('#mode').val(),
                batch_number: $('#batch_number').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                auto_switch_date: $('#auto_switch_date').val(),
                max_capacity: $('#max_capacity').val(),
                is_active: $('#is_active').is(':checked') ? 1 : 0,
                // 텍스트 설정
                waitlist_button_text: $('#waitlist_button_text').val(),
                enrollment_button_text: $('#enrollment_button_text').val(),
                countdown_text_waitlist: $('#countdown_text_waitlist').val(),
                countdown_text_enrollment: $('#countdown_text_enrollment').val()
            },
            success: function(response) {
                if (response.success) {
                    // 성공 메시지 표시
                    $saveMessage.show().delay(3000).fadeOut();
                } else {
                    alert('오류: ' + response.data);
                }
            },
            error: function() {
                alert('설정 저장 중 오류가 발생했습니다.');
            },
            complete: function() {
                // 스피너 숨기기
                $spinner.hide();
            }
        });
    });

    // 빠른 모드 전환
    $('#quick-switch-mode').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.text();

        $btn.prop('disabled', true).text('전환중...');

        $.ajax({
            url: donlinee_enrollment_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'donlinee_switch_mode',
                nonce: donlinee_enrollment_admin.nonce
            },
            success: function(response) {
                if (response.success) {
                    // 페이지 새로고침
                    location.reload();
                } else {
                    alert('오류: ' + response.data);
                    $btn.prop('disabled', false).text(originalText);
                }
            },
            error: function() {
                alert('모드 전환 중 오류가 발생했습니다.');
                $btn.prop('disabled', false).text(originalText);
            }
        });
    });

    // 신청자 상태 변경
    $('.enrollment-status-select').on('change', function() {
        const $select = $(this);
        const id = $select.data('id');
        const status = $select.val();

        $.ajax({
            url: donlinee_enrollment_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'donlinee_update_enrollment_status',
                nonce: donlinee_enrollment_admin.nonce,
                id: id,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    // 배경색 변경 효과
                    const $row = $select.closest('tr');
                    $row.css('background-color', '#e8f5e9');
                    setTimeout(function() {
                        $row.css('background-color', '');
                    }, 1000);
                } else {
                    alert('상태 변경 실패: ' + response.data);
                    // 원래 값으로 복구
                    location.reload();
                }
            },
            error: function() {
                alert('상태 변경 중 오류가 발생했습니다.');
                location.reload();
            }
        });
    });

    // 신청 삭제
    $('.delete-enrollment-btn').on('click', function() {
        if (!confirm('정말로 이 신청을 삭제하시겠습니까?')) {
            return;
        }

        const $btn = $(this);
        const id = $btn.data('id');

        $btn.prop('disabled', true).text('삭제중...');

        $.ajax({
            url: donlinee_enrollment_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'donlinee_delete_enrollment',
                nonce: donlinee_enrollment_admin.nonce,
                id: id
            },
            success: function(response) {
                if (response.success) {
                    // 행 제거
                    $btn.closest('tr').fadeOut(500, function() {
                        $(this).remove();
                    });
                } else {
                    alert('삭제 실패: ' + response.data);
                    $btn.prop('disabled', false).text('삭제');
                }
            },
            error: function() {
                alert('삭제 중 오류가 발생했습니다.');
                $btn.prop('disabled', false).text('삭제');
            }
        });
    });

    // 상세보기
    $('.view-details').on('click', function() {
        const id = $(this).data('id');

        $.ajax({
            url: donlinee_enrollment_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'donlinee_get_enrollment_details',
                nonce: donlinee_enrollment_admin.nonce,
                id: id
            },
            success: function(response) {
                if (response.success) {
                    $('#enrollment-detail-content').html(response.data.html);
                    $('#enrollment-detail-modal').fadeIn();
                } else {
                    alert('상세정보를 불러올 수 없습니다.');
                }
            },
            error: function() {
                alert('오류가 발생했습니다.');
            }
        });
    });

    // 모달 닫기
    window.closeDetailModal = function() {
        $('#enrollment-detail-modal').fadeOut();
    };

    // 모달 외부 클릭으로 닫기
    $('#enrollment-detail-modal').on('click', function(e) {
        if ($(e.target).is('#enrollment-detail-modal')) {
            $(this).fadeOut();
        }
    });

    // CSV 내보내기
    $('#export-enrollments').on('click', function(e) {
        e.preventDefault();
        window.location.href = donlinee_enrollment_admin.ajax_url +
            '?action=donlinee_export_enrollments&nonce=' + donlinee_enrollment_admin.nonce;
    });

    // 필터 적용
    $('#apply-filter').on('click', function() {
        const status = $('#status-filter').val();
        let url = window.location.href.split('?')[0] + '?page=donlinee-enrollment-list';

        if (status) {
            url += '&status=' + status;
        }

        window.location.href = url;
    });

    // 엔터 키로 필터 적용
    $('#status-filter').on('keypress', function(e) {
        if (e.which === 13) {
            $('#apply-filter').click();
        }
    });

    // 날짜/시간 입력 필드 min 속성 설정 제거
    // 모집 시작 후에도 날짜를 수정할 수 있도록 min 속성을 설정하지 않음
    // const now = new Date();
    // const localDateTime = now.getFullYear() + '-' +
    //                      String(now.getMonth() + 1).padStart(2, '0') + '-' +
    //                      String(now.getDate()).padStart(2, '0') + 'T' +
    //                      String(now.getHours()).padStart(2, '0') + ':' +
    //                      String(now.getMinutes()).padStart(2, '0');
    // $('#start_date, #end_date, #auto_switch_date').attr('min', localDateTime);

    // 텍스트 기본값 복원 버튼
    $('#reset-texts-default').on('click', function() {
        if (confirm('기본 텍스트로 복원하시겠습니까?')) {
            $('#waitlist_button_text').val('수강 대기신청');
            $('#enrollment_button_text').val('(OPEN) 수강 신청하기');
            $('#countdown_text_waitlist').val('모집 시작까지');
            $('#countdown_text_enrollment').val('모집 마감까지');
        }
    });

    // 텍스트 입력 필드 실시간 미리보기
    $('#waitlist_button_text, #enrollment_button_text').on('input', function() {
        const currentMode = $('#mode').val();
        const previewText = currentMode === 'enrollment' ?
                           $('#enrollment_button_text').val() :
                           $('#waitlist_button_text').val();

        // 미리보기 업데이트 (선택사항)
        // $('.preview-button-text').text(previewText);
    });

    // 대기 신청 플러그인과의 연동 체크
    if (typeof donlinee_waitlist_admin !== 'undefined') {
        console.log('대기 신청 플러그인과 연동됨');
    }
});