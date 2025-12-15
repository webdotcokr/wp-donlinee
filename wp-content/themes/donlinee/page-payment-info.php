<?php
/**
 * Template Name: Payment Info Template
 * Description: 수강료 납부 안내 페이지 템플릿
 *
 * @package Donlinee
 * @author webdot
 */

get_header(); ?>

<main class="bg-gray-50 min-h-screen py-12 md:py-20">
    <div class="container max-w-2xl mx-auto px-4 md:px-6">
        <!-- 페이지 제목 -->
        <h1 class="text-3xl md:text-4xl font-bold mb-8 md:mb-12 text-center text-gray-800">
            수강료 납부 안내
        </h1>

        <!-- 메인 카드 -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- 헤더 섹션 -->
            <div class="bg-gradient-to-r from-gray-900 to-black p-6 md:p-8 text-white">
                <h2 class="text-xl md:text-2xl font-bold mb-2">계좌이체 정보</h2>
                <p class="text-sm md:text-base opacity-90">아래 계좌로 수강료를 입금해 주세요<br/>입금 확인 이후, 수강 접수가 완료됩니다.</p>
            </div>

            <!-- 계좌 정보 섹션 -->
            <div class="p-6 md:p-8 space-y-6">
                <!-- 은행 정보 -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">은행명</p>
                            <p class="text-lg font-semibold text-gray-800">하나은행</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">예금주</p>
                            <p class="text-lg font-semibold text-gray-800">박래완</p>
                        </div>
                    </div>

                    <!-- 계좌번호 -->
                    <div class="mb-4">
                        <p class="text-sm text-gray-500 mb-2">계좌번호</p>
                        <div class="flex items-center justify-between bg-white border-2 border-gray-200 rounded-lg p-4">
                            <span id="account-number" class="text-lg md:text-xl font-bold text-gray-900">562-910513-14907</span>
                            <button onclick="copyAccountNumber()" class="bg-black hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                복사
                            </button>
                        </div>
                        <div id="copy-message" class="text-sm text-green-600 mt-2 opacity-0 transition-opacity duration-300"></div>
                    </div>

                    <!-- 금액 -->
                    <div>
                        <p class="text-sm text-gray-500 mb-2">신청 금액</p>
                        <div class="bg-gray-100 border border-gray-300 rounded-lg p-4">
                            <p class="text-2xl md:text-3xl font-bold text-gray-900">1,980,000원</p>
                            <p class="text-sm text-gray-600 mt-1">(일백구십팔만원)</p>
                        </div>
                    </div>
                </div>

                <!-- 안내사항 -->
                <div class="space-y-3">
                    <h3 class="font-bold text-gray-800 text-lg">📌 입금 시 유의사항</h3>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start">
                            <span class="text-black mr-2">•</span>
                            <span>입금자명은 <strong>신청하신 성함과 동일</strong>하게 입력해 주세요</span>
                        </li>
                    </ul>
                </div>

                <!-- 문의 정보 -->
                <div class="bg-gray-100 border border-gray-300 rounded-lg p-4">
                    <p class="text-sm text-gray-700">
                        <strong>💬 문의사항이 있으신가요?</strong><br>
                        카카오톡 채널 통해 문의해주시면 답변 드리고 있습니다.
                    </p>
                </div>
            </div>
        </div>

        <!-- 추가 안내 -->
        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>본 페이지는 수강료 납부를 위한 안내 페이지입니다.</p>
            <p>수강 신청은 홈페이지에서 진행해 주세요.</p>
        </div>
    </div>
</main>

<!-- 계좌번호 복사 스크립트 -->
<script>
function copyAccountNumber() {
    const accountNumber = document.getElementById('account-number').textContent;
    const copyMessage = document.getElementById('copy-message');

    // 클립보드에 복사
    if (navigator.clipboard && window.isSecureContext) {
        // navigator.clipboard API 사용 (HTTPS 환경)
        navigator.clipboard.writeText(accountNumber).then(function() {
            showCopyMessage('계좌번호가 복사되었습니다!');
        }, function(err) {
            // 실패 시 fallback
            fallbackCopyTextToClipboard(accountNumber);
        });
    } else {
        // HTTP 환경이거나 clipboard API를 지원하지 않는 경우
        fallbackCopyTextToClipboard(accountNumber);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;

    // iOS 대응
    textArea.style.position = "fixed";
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.width = "2em";
    textArea.style.height = "2em";
    textArea.style.padding = "0";
    textArea.style.border = "none";
    textArea.style.outline = "none";
    textArea.style.boxShadow = "none";
    textArea.style.background = "transparent";

    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showCopyMessage('계좌번호가 복사되었습니다!');
        } else {
            showCopyMessage('복사에 실패했습니다. 직접 선택해서 복사해주세요.');
        }
    } catch (err) {
        showCopyMessage('복사에 실패했습니다. 직접 선택해서 복사해주세요.');
    }

    document.body.removeChild(textArea);
}

function showCopyMessage(message) {
    const copyMessage = document.getElementById('copy-message');
    copyMessage.textContent = message;
    copyMessage.style.opacity = '1';

    setTimeout(function() {
        copyMessage.style.opacity = '0';
    }, 3000);
}
</script>

<!-- 모바일 최적화 스타일 -->
<style>
/* 모바일에서 텍스트 선택 가능하게 */
#account-number {
    -webkit-user-select: text;
    -moz-user-select: text;
    -ms-user-select: text;
    user-select: text;
}

/* 버튼 터치 영역 최적화 */
@media (max-width: 768px) {
    button, a {
        min-height: 44px;
        touch-action: manipulation;
    }
}

/* 스크롤 부드럽게 */
html {
    scroll-behavior: smooth;
}

/* 카드 그림자 부드럽게 */
.shadow-lg {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
}
</style>

<?php get_footer(); ?>