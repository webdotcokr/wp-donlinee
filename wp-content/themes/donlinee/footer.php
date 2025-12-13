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

<!-- 하단 고정 배너 (iOS 최적화) -->
<div id="fixed-banner" class="fixed z-50 w-full lg:w-[50vw] px-4 lg:px-0 bottom-[30px] max-md:bottom-0 max-md:px-0"
     style="display: none; left: 0; right: 0; margin: 0 auto; transform: none;">
    <div class="bg-gradient-to-r from-[#2c2c2c] to-[#3a3a3a] text-white rounded-lg max-md:rounded-none shadow-2xl border border-gray-700">
        <div class="px-4 sm:px-6 py-4">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-4">

                <!-- 모집 일정 및 카피라이팅 -->
                <div class="flex flex-col items-center lg:items-start text-center lg:text-left">
                    <div class="text-xs text-gray-400 mb-1 max-md:hidden">모집일정: 2025.12.13(토) 오전 11시 ~ 12.28(일)</div>
                    <div class="text-sm sm:text-base font-medium">
                        강제 실행형 사업 강의, <span class="text-[#ef4444] font-semibold">돈마고치 1기</span>
                        <span class="block lg:inline text-xs sm:text-sm opacity-90 mt-1 lg:mt-0 lg:ml-2">20명 한정 | 100% 환불보장</span>
                    </div>
                </div>

                <div class="flex max-md:items-center max-md:space-between gap-8">
                    <!-- 타이머 섹션 -->
                    <div class="flex flex-col items-center lg:items-start">
                        <div id="countdown-label" class="text-xs text-gray-300 mb-1">모집 시작까지</div>
                        <div id="countdown-timer" class="text-2xl font-bold tracking-wider max-md:text-base" style="font-family: 'Pretendard', -apple-system, BlinkMacSystemFont, sans-serif;">
                            <span id="days">00</span>일
                            <span id="hours">00</span>:
                            <span id="minutes">00</span>:
                            <span id="seconds">00</span>
                        </div>
                    </div>

                    <!-- 신청 버튼 -->
                    <div class="flex items-center">
                        <a href="#" class="donlinee-waitlist-trigger inline-flex items-center bg-[#ef4444] hover:bg-[#dc2626] text-white px-6 py-3 max-md:px-6 max-md:py-2 rounded-md text-sm font-bold transition-colors shadow-lg">
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

<!-- 카카오톡 플로팅 버튼 -->
<div id="kakao-floating-button" class="fixed z-[49]"
     style="bottom: 40px; right: 30px;">
    <a href="http://pf.kakao.com/_cnxlPn" target="_blank" rel="noopener noreferrer"
       class="block w-[60px] h-[60px] lg:w-[70px] lg:h-[70px] rounded-full shadow-lg"
       aria-label="카카오톡 채널 상담">
        <img src="/wp-content/uploads/2025/12/kakaotalk.webp"
             alt="카카오톡 상담"
             class="w-full h-full object-contain"
             loading="lazy">
    </a>
</div>

<style>
/* iOS 최적화 스타일 */
.hidden-banner {
    opacity: 0 !important;
    visibility: hidden !important;
}

/* 카카오톡 버튼 모바일 위치 */
@media (max-width: 768px) {
    #kakao-floating-button {
        bottom: 150px !important;
        right: 10px !important;
    }
}

/* iOS에서 펄스 애니메이션 비활성화 */
@supports (-webkit-touch-callout: none) {
    #kakao-floating-button::before {
        display: none !important;
    }
}
</style>

  <?php wp_footer(); ?>

  <!-- iOS 최적화 스크립트 로드 -->
  <script src="<?php echo get_template_directory_uri(); ?>/js/ios-optimization.js"></script>

  <!-- Swiper 로드 (iOS 조건부) -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-element-bundle.min.js"></script>

  <script>
    // iOS 감지
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

    // Swiper 초기화 - iOS 최적화
    document.addEventListener('DOMContentLoaded', function() {
        const testimonialSwiper = new Swiper('.testimonial-swiper', {
            slidesPerView: 'auto',
            spaceBetween: 20,
            speed: isIOS ? 500 : 3000,
            loop: true,
            autoplay: isIOS ? {
                delay: 5000, // iOS: 5초 간격
                disableOnInteraction: true,
                pauseOnMouseEnter: true
            } : {
                delay: 3000, // 기타: 3초 간격 (0은 너무 과도함)
                disableOnInteraction: false,
                pauseOnMouseEnter: true
            },
            freeMode: !isIOS,
            freeModeMomentum: false,
            // iOS 전용 설정
            cssMode: isIOS, // CSS 기반 스크롤
            watchSlidesProgress: !isIOS,
            preloadImages: !isIOS,
            lazy: isIOS
        });
    });

    // 하단 고정 배너 스크립트 (iOS 최적화)
    document.addEventListener('DOMContentLoaded', function() {
        const banner = document.getElementById('fixed-banner');

        // 배너 초기 표시
        if (banner) {
            banner.style.display = 'block';
            setTimeout(() => {
                banner.style.opacity = '1';
                banner.style.visibility = 'visible';
            }, 100);
        }

        // 스크롤 이벤트 최적화 (Throttling)
        let scrollTimeout = null;
        let lastScrollTop = 0;

        function handleBannerVisibility() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const documentHeight = document.documentElement.scrollHeight;
            const windowHeight = window.innerHeight;
            const footerHeight = document.querySelector('footer')?.offsetHeight || 0;
            const fadeOutThreshold = documentHeight - footerHeight - 150;

            if (banner) {
                if (scrollTop + windowHeight > fadeOutThreshold) {
                    banner.classList.add('hidden-banner');
                } else {
                    banner.classList.remove('hidden-banner');
                }
            }

            lastScrollTop = scrollTop;
        }

        // iOS에서는 throttling 적용
        if (isIOS) {
            window.addEventListener('scroll', function() {
                if (scrollTimeout) {
                    clearTimeout(scrollTimeout);
                }
                scrollTimeout = setTimeout(handleBannerVisibility, 50); // 50ms throttle
            }, { passive: true });
        } else {
            // 일반 브라우저
            let ticking = false;
            window.addEventListener('scroll', function() {
                if (!ticking) {
                    window.requestAnimationFrame(function() {
                        handleBannerVisibility();
                        ticking = false;
                    });
                    ticking = true;
                }
            });
        }

        // 초기 실행
        handleBannerVisibility();

        // 카운트다운 타이머 (iOS 최적화)
        function updateCountdown() {
            <?php
            if (class_exists('Donlinee_Enrollment_Settings')) {
                $enrollment_settings = Donlinee_Enrollment_Settings::get_current_settings();
                $current_mode = $enrollment_settings['mode'];
                $start_date = $enrollment_settings['start_date'];
                $end_date = $enrollment_settings['end_date'];
            } else {
                $current_mode = 'waitlist';
                $start_date = '2025-12-13 11:00:00';
                $end_date = '2025-12-28 23:59:59';
            }
            ?>

            const currentMode = '<?php echo $current_mode; ?>';
            const startDate = '<?php echo str_replace(' ', 'T', $start_date); ?>+09:00';
            const endDate = '<?php echo str_replace(' ', 'T', $end_date); ?>+09:00';

            const targetDate = currentMode === 'waitlist' ?
                new Date(startDate).getTime() :
                new Date(endDate).getTime();

            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance < 0) {
                clearInterval(countdownInterval);
                const timerEl = document.getElementById('countdown-timer');
                if (timerEl) {
                    timerEl.innerHTML = '<span class="text-[#ef4444]">모집 중!</span>';
                }
                const labelEl = document.getElementById('countdown-label');
                if (labelEl) {
                    labelEl.textContent = '현재 모집 중';
                }
                return;
            }

            // 시간 계산
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // DOM 업데이트 (캐싱)
            const elements = {
                days: document.getElementById('days'),
                hours: document.getElementById('hours'),
                minutes: document.getElementById('minutes'),
                seconds: document.getElementById('seconds')
            };

            if (elements.days) elements.days.textContent = String(days).padStart(2, '0');
            if (elements.hours) elements.hours.textContent = String(hours).padStart(2, '0');
            if (elements.minutes) elements.minutes.textContent = String(minutes).padStart(2, '0');
            if (elements.seconds) elements.seconds.textContent = String(seconds).padStart(2, '0');
        }

        // 초기 실행
        updateCountdown();

        // iOS에서는 visibility API 활용
        let countdownInterval;

        function startTimer() {
            if (!countdownInterval) {
                countdownInterval = setInterval(updateCountdown, 1000);
            }
        }

        function stopTimer() {
            if (countdownInterval) {
                clearInterval(countdownInterval);
                countdownInterval = null;
            }
        }

        // 타이머 시작
        startTimer();

        // iOS에서 백그라운드 처리
        if (isIOS) {
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    stopTimer();
                } else {
                    startTimer();
                    updateCountdown(); // 즉시 업데이트
                }
            });
        }
    });

    // 챕터 레벨 아코디언 기능
    function toggleChapter(button) {
        const chapterItem = button.closest('.chapter-item');
        const chapterContent = chapterItem.querySelector('.chapter-content');
        const arrow = button.querySelector('.chapter-arrow');

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
            // iOS에서는 즉시 적용
            if (isIOS) {
                chapterContent.classList.add('show');
            } else {
                setTimeout(() => {
                    chapterContent.classList.add('show');
                }, 10);
            }
            arrow.classList.add('rotate');
        }
    }
  </script>
</body>

</html>