/**
 * iOS WebKit Performance Optimization
 * iOS Safari/Chrome에서 발생하는 성능 문제 해결
 */

(function() {
    'use strict';

    // iOS 디바이스 감지
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
    const isIOSSafari = isIOS && isSafari;

    // iOS 버전 감지
    const iOSVersion = isIOS ? parseInt(
        (navigator.userAgent.match(/OS (\d+)_/) || [])[1], 10
    ) : null;

    console.log('iOS Detection:', { isIOS, isSafari, isIOSSafari, iOSVersion });

    // iOS 최적화 클래스 추가
    if (isIOS) {
        document.documentElement.classList.add('ios-device');
        if (iOSVersion) {
            document.documentElement.classList.add('ios-' + iOSVersion);
        }
    }

    // 1. Swiper.js 최적화
    function optimizeSwiper() {
        if (!window.Swiper || !isIOS) return;

        // 기존 Swiper 인스턴스 찾기
        const swiperElements = document.querySelectorAll('.testimonial-swiper');

        swiperElements.forEach(element => {
            // 기존 인스턴스 제거
            if (element.swiper) {
                element.swiper.destroy();
            }

            // iOS 최적화된 설정으로 재초기화
            new Swiper(element, {
                slidesPerView: 'auto',
                spaceBetween: 20,
                speed: isIOS ? 500 : 3000, // iOS에서 더 빠른 전환
                loop: true,
                autoplay: isIOS ? {
                    delay: 5000, // iOS에서 5초 간격
                    disableOnInteraction: true,
                    pauseOnMouseEnter: true
                } : {
                    delay: 0,
                    disableOnInteraction: false
                },
                freeMode: !isIOS, // iOS에서 freeMode 비활성화
                cssMode: isIOS, // iOS에서 CSS 모드 사용 (더 부드러움)
                // iOS 전용 설정
                ...(isIOS && {
                    watchSlidesProgress: false,
                    preloadImages: false,
                    lazy: true,
                    effect: 'slide' // fade 효과 대신 slide 사용
                })
            });
        });
    }

    // 2. 스크롤 이벤트 최적화 (Throttling)
    function optimizeScrollEvents() {
        const banner = document.getElementById('fixed-banner');
        if (!banner) return;

        let scrollTimeout;
        let lastScrollTop = 0;
        let ticking = false;

        function handleScroll() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            // 스크롤 방향 감지 (iOS 최적화)
            const scrollDirection = scrollTop > lastScrollTop ? 'down' : 'up';
            lastScrollTop = scrollTop;

            // requestAnimationFrame으로 부드러운 처리
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    updateBannerVisibility(scrollTop, scrollDirection);
                    ticking = false;
                });
                ticking = true;
            }
        }

        function updateBannerVisibility(scrollTop, direction) {
            const documentHeight = document.documentElement.scrollHeight;
            const windowHeight = window.innerHeight;
            const footerHeight = document.querySelector('footer')?.offsetHeight || 0;
            const fadeOutThreshold = documentHeight - footerHeight - 150;

            if (scrollTop + windowHeight > fadeOutThreshold) {
                // CSS 클래스로 제어 (inline style 대신)
                banner.classList.add('hidden-banner');
            } else {
                banner.classList.remove('hidden-banner');
            }
        }

        // Passive 리스너 사용 (iOS 성능 개선)
        let supportsPassive = false;
        try {
            const opts = Object.defineProperty({}, 'passive', {
                get: function() {
                    supportsPassive = true;
                }
            });
            window.addEventListener('testPassive', null, opts);
            window.removeEventListener('testPassive', null, opts);
        } catch (e) {}

        // Throttled 스크롤 이벤트
        if (isIOS) {
            let scrollTimer;
            window.addEventListener('scroll', function() {
                if (scrollTimer) {
                    clearTimeout(scrollTimer);
                }
                scrollTimer = setTimeout(handleScroll, 16); // ~60fps
            }, supportsPassive ? { passive: true } : false);
        } else {
            // 일반 브라우저는 기존 방식
            window.addEventListener('scroll', handleScroll,
                supportsPassive ? { passive: true } : false);
        }
    }

    // 3. 애니메이션 최적화
    function optimizeAnimations() {
        if (!isIOS) return;

        // 무한 스크롤 애니메이션 비활성화/단순화
        const scrollElements = document.querySelectorAll('.animate-scroll');
        scrollElements.forEach(el => {
            if (iOSVersion && iOSVersion < 15) {
                // iOS 14 이하에서는 애니메이션 완전 비활성화
                el.style.animation = 'none';
            } else {
                // iOS 15+에서는 속도 조절
                el.style.animationDuration = '40s'; // 기존 25s에서 늘림
            }
        });

        // 펄스 애니메이션 비활성화
        const pulseElements = document.querySelectorAll('#kakao-floating-button::before');
        pulseElements.forEach(el => {
            if (isIOS) {
                el.style.animation = 'none';
            }
        });
    }

    // 4. 고정 배너 최적화
    function optimizeFixedBanner() {
        if (!isIOS) return;

        const banner = document.getElementById('fixed-banner');
        if (!banner) return;

        // transform 제거하고 margin auto로 중앙 정렬
        banner.style.transform = 'none';
        banner.style.left = '0';
        banner.style.right = '0';
        banner.style.marginLeft = 'auto';
        banner.style.marginRight = 'auto';

        // will-change 제거 (iOS에서 오히려 성능 저하)
        banner.style.willChange = 'auto';

        // transition 단순화
        banner.style.transition = 'opacity 0.3s ease';
    }

    // 5. 타이머 최적화 (iOS 전용)
    function optimizeTimer() {
        if (!isIOS) return;

        // 기존 타이머 함수 덮어쓰기
        const originalUpdateCountdown = window.updateCountdown;
        if (typeof originalUpdateCountdown === 'function') {
            let updateScheduled = false;

            window.updateCountdown = function() {
                if (!updateScheduled) {
                    updateScheduled = true;
                    // requestIdleCallback 사용 (iOS 15+)
                    if ('requestIdleCallback' in window) {
                        requestIdleCallback(() => {
                            originalUpdateCountdown.apply(this, arguments);
                            updateScheduled = false;
                        });
                    } else {
                        setTimeout(() => {
                            originalUpdateCountdown.apply(this, arguments);
                            updateScheduled = false;
                        }, 100);
                    }
                }
            };
        }
    }

    // 6. 이미지 최적화
    function optimizeImages() {
        if (!isIOS) return;

        // loading="lazy" 속성 추가
        const images = document.querySelectorAll('img:not([loading])');
        images.forEach(img => {
            img.loading = 'lazy';

            // iOS에서 큰 이미지 디코딩 최적화
            if (img.naturalWidth > 1000 || img.naturalHeight > 1000) {
                img.decoding = 'async';
            }
        });
    }

    // 7. 폰트 로딩 최적화
    function optimizeFonts() {
        if (!isIOS) return;

        // font-display: swap 적용
        const style = document.createElement('style');
        style.textContent = `
            @font-face {
                font-display: swap;
            }
        `;
        document.head.appendChild(style);
    }

    // 8. 메모리 관리
    function setupMemoryManagement() {
        if (!isIOS) return;

        // 페이지 숨김 시 리소스 정리
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                // 불필요한 타이머 정리
                const highResTimers = [];
                for (let i = 1; i < 99999; i++) {
                    if (window.clearTimeout(i)) {
                        highResTimers.push(i);
                    }
                }

                // 애니메이션 일시 중지
                document.querySelectorAll('*').forEach(el => {
                    const animationName = getComputedStyle(el).animationName;
                    if (animationName !== 'none') {
                        el.style.animationPlayState = 'paused';
                    }
                });
            } else {
                // 애니메이션 재개
                document.querySelectorAll('*').forEach(el => {
                    if (el.style.animationPlayState === 'paused') {
                        el.style.animationPlayState = 'running';
                    }
                });
            }
        });
    }

    // 초기화 함수
    function initialize() {
        if (!isIOS) {
            console.log('Not iOS device, skipping optimizations');
            return;
        }

        console.log('Applying iOS optimizations...');

        // DOM 준비 확인
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', applyOptimizations);
        } else {
            applyOptimizations();
        }
    }

    function applyOptimizations() {
        optimizeSwiper();
        optimizeScrollEvents();
        optimizeAnimations();
        optimizeFixedBanner();
        optimizeTimer();
        optimizeImages();
        optimizeFonts();
        setupMemoryManagement();

        console.log('iOS optimizations applied');

        // 성능 모니터링
        if (window.performance && window.performance.mark) {
            window.performance.mark('ios-optimizations-complete');
        }
    }

    // 실행
    initialize();

    // 전역 노출 (디버깅용)
    window.iOSOptimizations = {
        isIOS,
        iOSVersion,
        reapplyOptimizations: applyOptimizations
    };

})();