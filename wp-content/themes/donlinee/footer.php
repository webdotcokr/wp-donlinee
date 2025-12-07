<footer>
<div class="py-8 text-[#8a8a8a] text-[13px] border-t border-gray-300">
    <div class="container max-w-6xl mx-auto px-6">
        <div class="flex flex-col gap-y-3">

            <!-- 사업자 정보 -->
            <div class="flex flex-col gap-y-1 text-[#8a8a8a]">
                <div class="flex items-center">
                    <span class="w-[140px]">등록번호 :</span>
                    <span>565-33-01595</span>
                </div>
                <div class="flex items-center">
                    <span class="w-[140px]">상호 :</span>
                    <span>돈린이</span>
                </div>
                <div class="flex items-center">
                    <span class="w-[140px]">대표 :</span>
                    <span>곽경환</span>
                </div>
                <div class="flex items-center">
                    <span class="w-[140px]">사업장소재지 :</span>
                    <span>서울 강남구 역삼로3길 19</span>
                </div>
            </div>

            <!-- 링크 줄 -->
            <div class="flex flex-wrap items-center gap-x-8 pt-4 border-t border-gray-300 text-gray-600">
                <a href="/privacy" class="hover:text-gray-800">개인정보 처리방침</a>
                <a href="/terms" class="hover:text-gray-800">이용약관</a>
            </div>
        </div>
    </div>
<div>

</div>
</footer>

<!-- 하단 고정 배너 -->
<div id="fixed-banner" class="fixed left-1/2 transform -translate-x-1/2 z-50 w-full max-w-[600px] px-4 lg:px-0" style="bottom: 40px; display: none;">
    <div class="bg-[#3a3a3a] text-white rounded-lg shadow-2xl">
        <div class="px-6 py-4">
            <div class="flex items-center justify-center gap-6">
                <!-- 모집 시작까지 텍스트 -->
                <div class="text-sm text-gray-300 whitespace-nowrap">
                    모집 시작까지
                </div>

                <!-- 타이머 -->
                <div id="countdown-timer" class="text-2xl font-bold tracking-wide" style="font-family: 'Pretendard', -apple-system, BlinkMacSystemFont, sans-serif;">
                    <span id="days">00</span>일
                    <span id="hours">00</span>:<span id="minutes">00</span>:<span id="seconds">00</span>
                </div>

                <!-- 신청 버튼 -->
                <a href="#" class="donlinee-waitlist-trigger bg-[#ef4444] hover:bg-[#dc2626] text-white px-6 py-2.5 rounded text-sm font-bold transition-all duration-200 whitespace-nowrap">
                    수강 대기신청 →
                </a>
            </div>
        </div>

        <!-- 하단 추가 정보 -->
        <div class="bg-[#2c2c2c] px-6 py-2 text-center">
            <div class="text-xs text-gray-400">
                비즈니스 PT 수익화 트레이닝, 지금 신청하세요!
                <span class="text-gray-500 mx-2">|</span>
                선착 후 불만족 시 100% 환불
            </div>
        </div>
    </div>
</div>

  <?php wp_footer(); ?>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-element-bundle.min.js"></script>

  <script>
    // Swiper 초기화 - Infinite Slide
    const testimonialSwiper = new Swiper('.testimonial-swiper', {
      slidesPerView: 'auto',
      spaceBetween: 20,
      speed: 3000,
      loop: true,
      autoplay: {
        delay: 0,
        disableOnInteraction: false,
      },
      freeMode: true,
      freeModeMomentum: false,
    });

    // 하단 고정 배너 스크립트
    document.addEventListener('DOMContentLoaded', function() {
        const banner = document.getElementById('fixed-banner');

        // 배너 항상 표시
        if (banner) {
            banner.style.display = 'block';
        }

        // 카운트다운 타이머
        function updateCountdown() {
            // 목표 날짜: 2025년 12월 13일 오전 11시
            const targetDate = new Date('2025-12-13T11:00:00+09:00').getTime();
            const now = new Date().getTime();
            const distance = targetDate - now;

            // 시간 계산
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // DOM 업데이트
            const daysEl = document.getElementById('days');
            const hoursEl = document.getElementById('hours');
            const minutesEl = document.getElementById('minutes');
            const secondsEl = document.getElementById('seconds');

            if (daysEl) daysEl.textContent = String(days).padStart(2, '0');
            if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
            if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
            if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');

            // 타이머 종료 처리
            if (distance < 0) {
                clearInterval(countdownInterval);
                const timerEl = document.getElementById('countdown-timer');
                if (timerEl) {
                    timerEl.innerHTML = '<span class="text-[#ef4444]">모집 중!</span>';
                }
                // 모집 시작까지 텍스트 변경
                const labelEl = timerEl?.previousElementSibling;
                if (labelEl) {
                    labelEl.textContent = '현재 모집 중';
                }
            }
        }

        // 초기 실행
        updateCountdown();

        // 1초마다 업데이트
        const countdownInterval = setInterval(updateCountdown, 1000);

        // 대기신청 버튼 클릭 이벤트 (팝업 트리거)
        document.querySelectorAll('.donlinee-waitlist-trigger').forEach(function(trigger) {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                // jQuery 이벤트로 팝업 열기
                if (typeof jQuery !== 'undefined') {
                    jQuery('#donlinee-waitlist-popup').fadeIn(300);
                    jQuery('body').css('overflow', 'hidden');
                }
            });
        });
    });
  </script>
</body>

</html>