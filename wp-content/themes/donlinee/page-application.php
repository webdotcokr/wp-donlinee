<?php
/**
 * Template Name: Application Page
 *
 * @package Donlinee
 * @author webdot
 */

get_header(); ?>

<main id="main" class="site-main">
    <section class="py-[80px] max-w-[600px] mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="h1 hero-title mb-4">돈마고치 수강 신청</h1>
            <p class="text-xl text-gray-600">4주 안에 월 천만원 벌기 프로젝트</p>
        </div>

        <form id="application-form" class="bg-white rounded-lg shadow-lg p-8">
            <div class="mb-6">
                <label for="name" class="block text-lg font-semibold mb-2">이름 <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                       placeholder="실명을 입력해주세요">
            </div>

            <div class="mb-6">
                <label for="age" class="block text-lg font-semibold mb-2">나이 <span class="text-red-500">*</span></label>
                <input type="number" id="age" name="age" required min="1" max="100"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                       placeholder="나이를 입력해주세요">
            </div>

            <div class="mb-6">
                <label for="phone" class="block text-lg font-semibold mb-2">전화번호 <span class="text-red-500">*</span></label>
                <input type="tel" id="phone" name="phone" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                       placeholder="010-0000-0000"
                       pattern="[0-9]{3}-[0-9]{3,4}-[0-9]{4}">
                <small class="text-gray-500">* 카카오톡 알림톡이 발송될 번호입니다.</small>
            </div>

            <div class="mb-8">
                <label class="flex items-start">
                    <input type="checkbox" id="agree" name="agree" required class="mt-1 mr-2">
                    <span class="text-sm text-gray-600">
                        개인정보 수집 및 이용에 동의합니다.<br>
                        수집항목: 이름, 나이, 전화번호<br>
                        이용목적: 강의 안내 및 알림톡 발송<br>
                        보유기간: 강의 종료 후 1년
                    </span>
                </label>
            </div>

            <button type="submit" id="submit-btn"
                    class="w-full bg-red-600 text-white py-4 px-6 rounded-lg text-lg font-bold hover:bg-red-700 transition-colors">
                수강 신청하기
            </button>
        </form>

        <div id="result-message" class="mt-6 p-4 rounded-lg hidden"></div>
    </section>

    <!-- 강의 정보 -->
    <section class="py-[60px] bg-gray-50">
        <div class="max-w-[800px] mx-auto px-4">
            <h2 class="h2 text-center mb-12">강의 정보</h2>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-lg">
                    <h3 class="font-bold text-xl mb-3">📅 진행 일정</h3>
                    <p>2025년 1월 ~ 2월<br>매주 토/일 오후 2시~5시</p>
                </div>

                <div class="bg-white p-6 rounded-lg">
                    <h3 class="font-bold text-xl mb-3">📍 진행 장소</h3>
                    <p>서울 강남역 인근<br>(상세 위치는 합격자 개별 안내)</p>
                </div>

                <div class="bg-white p-6 rounded-lg">
                    <h3 class="font-bold text-xl mb-3">💰 수강료</h3>
                    <p>198만원<br>(카드 할부 가능)</p>
                </div>

                <div class="bg-white p-6 rounded-lg">
                    <h3 class="font-bold text-xl mb-3">👥 모집 인원</h3>
                    <p>20명 선착순<br>(50명 이상 지원시 조기 마감)</p>
                </div>
            </div>

            <div class="mt-12 text-center">
                <p class="text-lg text-gray-600">
                    <strong>💡 100% 환불 보장</strong><br>
                    정상 참여 후 매출이 발생하지 않으면 전액 환불해드립니다.
                </p>
            </div>
        </div>
    </section>
</main>

<script>
jQuery(document).ready(function($) {
    // Phone number formatting
    $('#phone').on('input', function() {
        var value = $(this).val().replace(/[^0-9]/g, '');
        var formatted = '';

        if(value.length <= 3) {
            formatted = value;
        } else if(value.length <= 6) {
            formatted = value.slice(0, 3) + '-' + value.slice(3);
        } else if(value.length <= 10) {
            formatted = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6);
        } else {
            formatted = value.slice(0, 3) + '-' + value.slice(3, 7) + '-' + value.slice(7, 11);
        }

        $(this).val(formatted);
    });

    // Form submission
    $('#application-form').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var $submitBtn = $('#submit-btn');
        var $resultMsg = $('#result-message');

        // Validate
        if(!$('#agree').is(':checked')) {
            alert('개인정보 수집 및 이용에 동의해주세요.');
            return;
        }

        // Disable button
        $submitBtn.prop('disabled', true).text('처리 중...');

        // Prepare data
        var formData = {
            name: $('#name').val(),
            age: $('#age').val(),
            phone: $('#phone').val(),
            course: '돈마고치'
        };

        // Send AJAX request
        $.ajax({
            url: '<?php echo rest_url('donlinee/v1/submit'); ?>',
            method: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: function(response) {
                // Success message
                $resultMsg
                    .removeClass('hidden bg-red-100 text-red-700')
                    .addClass('bg-green-100 text-green-700')
                    .html('<strong>✅ 접수가 완료되었습니다!</strong><br>카카오톡으로 안내 메시지가 발송됩니다.');

                // Reset form
                $form[0].reset();

                // Scroll to message
                $('html, body').animate({
                    scrollTop: $resultMsg.offset().top - 100
                }, 500);
            },
            error: function(xhr) {
                var errorMsg = '접수 중 오류가 발생했습니다.';

                if(xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }

                // Error message
                $resultMsg
                    .removeClass('hidden bg-green-100 text-green-700')
                    .addClass('bg-red-100 text-red-700')
                    .html('<strong>❌ ' + errorMsg + '</strong>');
            },
            complete: function() {
                // Re-enable button
                $submitBtn.prop('disabled', false).text('수강 신청하기');
            }
        });
    });
});
</script>

<style>
/* Custom styles for form */
input[type="text"]:focus,
input[type="number"]:focus,
input[type="tel"]:focus {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

button[type="submit"]:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Loading animation */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

button[type="submit"]:disabled {
    animation: pulse 2s infinite;
}
</style>

<?php get_footer(); ?>