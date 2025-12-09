(function($) {
    'use strict';

    $(document).ready(function() {

        // 필터 적용
        $('#apply-filter').on('click', function() {
            const status = $('#status-filter').val();
            let url = 'admin.php?page=donlinee-waitlist';
            if (status) {
                url += '&status=' + status;
            }
            window.location.href = url;
        });

        // 상태 변경
        $('.status-select').on('change', function() {
            const $this = $(this);
            const id = $this.data('id');
            const newStatus = $this.val();
            const originalValue = $this.data('original-value') || $this.val();

            if (confirm('상태를 변경하시겠습니까?')) {
                $.ajax({
                    url: donlinee_waitlist_admin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'donlinee_waitlist_update_status',
                        nonce: donlinee_waitlist_admin.nonce,
                        id: id,
                        status: newStatus
                    },
                    success: function(response) {
                        if (response.success) {
                            // 스타일 업데이트
                            $this.removeClass('pending confirmed cancelled').addClass(newStatus);
                            $this.data('original-value', newStatus);

                            // 알림 표시
                            showNotice('상태가 업데이트되었습니다.', 'success');

                            // 통계 카드 업데이트 (페이지 새로고침)
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            alert(response.data.message || '업데이트에 실패했습니다.');
                            $this.val(originalValue);
                        }
                    },
                    error: function() {
                        alert('서버 오류가 발생했습니다.');
                        $this.val(originalValue);
                    }
                });
            } else {
                $this.val(originalValue);
            }
        });

        // 삭제
        $('.delete-btn').on('click', function() {
            const $this = $(this);
            const id = $this.data('id');
            const $row = $this.closest('tr');

            if (confirm('정말 삭제하시겠습니까? 이 작업은 되돌릴 수 없습니다.')) {
                $.ajax({
                    url: donlinee_waitlist_admin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'donlinee_waitlist_delete',
                        nonce: donlinee_waitlist_admin.nonce,
                        id: id
                    },
                    success: function(response) {
                        if (response.success) {
                            $row.fadeOut(400, function() {
                                $(this).remove();

                                // 테이블이 비었는지 확인
                                if ($('tbody tr').length === 0) {
                                    $('tbody').html('<tr><td colspan="6" style="text-align: center;">신청 내역이 없습니다.</td></tr>');
                                }
                            });

                            showNotice('삭제되었습니다.', 'success');
                        } else {
                            alert(response.data.message || '삭제에 실패했습니다.');
                        }
                    },
                    error: function() {
                        alert('서버 오류가 발생했습니다.');
                    }
                });
            }
        });

        // CSV 내보내기
        $('#export-csv').on('click', function(e) {
            e.preventDefault();

            if (confirm('전체 신청 목록을 CSV 파일로 내보내시겠습니까?')) {
                window.location.href = donlinee_waitlist_admin.export_url;
            }
        });

        // Slack 테스트 알림
        $('#test-slack-waitlist').on('click', function() {
            if(!confirm('테스트 알림을 Slack에 발송하시겠습니까?')) {
                return;
            }

            var $btn = $(this);
            $btn.prop('disabled', true).text('발송 중...');

            $.ajax({
                url: donlinee_waitlist_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'test_slack_waitlist_notification',
                    nonce: donlinee_waitlist_admin.nonce
                },
                success: function(response) {
                    if(response.success) {
                        alert('✅ Slack 테스트 알림이 성공적으로 발송되었습니다!');
                    } else {
                        alert('❌ Slack 알림 발송 실패: ' + (response.data.message || '알 수 없는 오류'));
                    }
                },
                error: function() {
                    alert('❌ 요청 중 오류가 발생했습니다.');
                },
                complete: function() {
                    $btn.prop('disabled', false).text('Slack 테스트 알림 보내기');
                }
            });
        });

        // 알림 표시 함수
        function showNotice(message, type) {
            const noticeClass = type === 'success' ? 'notice-success' : 'notice-error';
            const $notice = $('<div class="notice ' + noticeClass + ' is-dismissible"><p>' + message + '</p></div>');

            $('.wrap h1').after($notice);

            // 3초 후 자동 제거
            setTimeout(function() {
                $notice.fadeOut(400, function() {
                    $(this).remove();
                });
            }, 3000);
        }

        // 초기 상태값 저장
        $('.status-select').each(function() {
            $(this).data('original-value', $(this).val());
        });

    });

})(jQuery);