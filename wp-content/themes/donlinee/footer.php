<footer>
<div class="border-t border-gray-300 py-8 text-[#8a8a8a] text-[13px]">
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
<div id="fixed-banner" class="fixed left-1/2 transform -translate-x-1/2 z-50 w-full lg:w-[50vw] px-4 lg:px-0 bottom-[30px] max-md:bottom-0 max-md:px-0" style="display: none; transition: opacity 0.5s ease-in-out, visibility 0.5s ease-in-out;">
    <div class="bg-gradient-to-r from-[#2c2c2c] to-[#3a3a3a] text-white rounded-lg max-md:rounded-none shadow-2xl border border-gray-700">
        <div class="px-4 sm:px-6 py-4">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-4">

                <!-- 모집 일정 및 카피라이팅 -->
                <div class="flex flex-col items-center lg:items-start text-center lg:text-left">
                    <div class="text-xs text-gray-400 mb-1 max-md:hidden">모집일정: 2025.12.13(토) ~ 12.28(일)</div>
                    <div class="text-sm sm:text-base font-medium">
                        강제 실행형 사업 강의, <span class="text-[#ef4444] font-semibold">돈마고치 1기</span>
                        <span class="block lg:inline text-xs sm:text-sm opacity-90 mt-1 lg:mt-0 lg:ml-2">20명 한정</span>
                        <!-- <span class="block lg:inline text-xs sm:text-sm opacity-90 mt-1 lg:mt-0 lg:ml-2">20명 한정 | 100% 환불보장</span> -->
                    </div>
                </div>

                <div class="flex max-md:items-center max-md:space-between gap-8">
                    <!-- 타이머 섹션 -->
                    <div class="flex flex-col items-center lg:items-start">
                        <div class="text-xs text-gray-300 mb-1">모집 시작까지</div>
                        <div id="countdown-timer" class="text-2xl font-bold tracking-wider max-md:text-base" style="font-family: 'Pretendard', -apple-system, BlinkMacSystemFont, sans-serif;">
                            <span id="days">00</span>일
                            <span id="hours">00</span>:
                            <span id="minutes">00</span>:
                            <span id="seconds">00</span>
                        </div>
                    </div>

                    <!-- 신청 버튼 -->
                    <div class="flex items-center">
                        <a href="#" class="donlinee-waitlist-trigger inline-flex items-center bg-[#ef4444] hover:bg-[#dc2626] text-white px-6 py-3 max-md:px-6 max-md:py-2 rounded-md text-sm font-bold transition-all duration-200 transform hover:scale-105 shadow-lg">
                            <span>수강 대기신청</span>
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
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

        // 배너 초기 표시
        if (banner) {
            banner.style.display = 'block';
            banner.style.opacity = '1';
            banner.style.visibility = 'visible';
        }

        // 스크롤 이벤트로 배너 페이드 인/아웃 처리
        let isScrolling = false;

        function handleBannerVisibility() {
            if (!isScrolling) {
                window.requestAnimationFrame(function() {
                    const scrollPosition = window.scrollY + window.innerHeight;
                    const documentHeight = document.documentElement.scrollHeight;
                    const footerHeight = document.querySelector('footer')?.offsetHeight || 0;

                    // 페이지 하단(푸터 영역)에 도달했는지 확인
                    // 푸터 높이 + 여유 공간(150px) 만큼 위에서부터 페이드아웃 시작
                    const fadeOutThreshold = documentHeight - footerHeight - 150;

                    if (banner) {
                        if (scrollPosition > fadeOutThreshold) {
                            // 푸터 근처에 도달하면 배너를 천천히 사라지게 함
                            banner.style.opacity = '0';
                            banner.style.visibility = 'hidden';
                        } else {
                            // 푸터에서 멀어지면 배너를 다시 표시
                            banner.style.opacity = '1';
                            banner.style.visibility = 'visible';
                        }
                    }

                    isScrolling = false;
                });
                isScrolling = true;
            }
        }

        // 스크롤 이벤트 리스너 추가
        window.addEventListener('scroll', handleBannerVisibility);

        // 초기 실행 (페이지 로드 시 현재 스크롤 위치 확인)
        handleBannerVisibility();

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

    // 챕터 레벨 아코디언 기능
    function toggleChapter(button) {
        // 챕터 아이템 요소 찾기
        const chapterItem = button.closest('.chapter-item');
        const chapterContent = chapterItem.querySelector('.chapter-content');
        const arrow = button.querySelector('.chapter-arrow');

        // 현재 챕터 토글
        if (chapterContent.classList.contains('show')) {
            chapterContent.classList.remove('show');
            chapterContent.style.display = 'none';
            arrow.classList.remove('rotate');

            // 비디오 정지
            const iframes = chapterContent.querySelectorAll('iframe');
            iframes.forEach(iframe => {
                const src = iframe.src;
                iframe.src = '';
                iframe.src = src;
            });
        } else {
            chapterContent.style.display = 'block';
            setTimeout(() => {
                chapterContent.classList.add('show');
            }, 10);
            arrow.classList.add('rotate');
        }
    }
  </script>
</body>

</html>