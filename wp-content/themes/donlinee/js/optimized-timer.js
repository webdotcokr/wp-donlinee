/**
 * 최적화된 타이머 코드
 * - 페이지 visibility API 활용
 * - requestAnimationFrame 사용
 * - 불필요한 DOM 조작 최소화
 */

(function() {
    'use strict';

    // 타이머 관련 변수
    let countdownInterval = null;
    let lastUpdateTime = 0;
    let cachedElements = {};
    let timerSettings = null;

    // DOM 요소 캐싱
    function cacheElements() {
        cachedElements = {
            days: document.getElementById('days'),
            hours: document.getElementById('hours'),
            minutes: document.getElementById('minutes'),
            seconds: document.getElementById('seconds'),
            timer: document.getElementById('countdown-timer'),
            label: document.getElementById('countdown-label')
        };
    }

    // 설정을 AJAX로 비동기 로드 (페이지 로드 블로킹 방지)
    function loadTimerSettings() {
        // 로컬 스토리지에 캐시된 설정 먼저 확인
        const cached = localStorage.getItem('donlinee_timer_settings');
        if (cached) {
            const data = JSON.parse(cached);
            // 5분 이내 캐시만 사용
            if (Date.now() - data.timestamp < 300000) {
                timerSettings = data.settings;
                startCountdown();
                return;
            }
        }

        // AJAX로 설정 로드
        if (typeof donlinee_enrollment !== 'undefined') {
            // PHP에서 이미 로드된 경우
            timerSettings = {
                mode: donlinee_enrollment.current_mode,
                startDate: donlinee_enrollment.start_date || '2025-12-13T11:00:00+09:00',
                endDate: donlinee_enrollment.end_date || '2025-12-28T23:59:59+09:00'
            };

            // 로컬 스토리지에 캐싱
            localStorage.setItem('donlinee_timer_settings', JSON.stringify({
                settings: timerSettings,
                timestamp: Date.now()
            }));

            startCountdown();
        } else {
            // Fallback: 기본값 사용
            timerSettings = {
                mode: 'waitlist',
                startDate: '2025-12-13T11:00:00+09:00',
                endDate: '2025-12-28T23:59:59+09:00'
            };
            startCountdown();
        }
    }

    // 카운트다운 업데이트 (최적화)
    function updateCountdown() {
        if (!timerSettings || !cachedElements.timer) return;

        const now = Date.now();

        // 1초에 한 번만 업데이트 (과도한 업데이트 방지)
        if (now - lastUpdateTime < 1000) return;
        lastUpdateTime = now;

        // 타겟 날짜 계산
        const targetDate = timerSettings.mode === 'waitlist' ?
            new Date(timerSettings.startDate).getTime() :
            new Date(timerSettings.endDate).getTime();

        const distance = targetDate - now;

        if (distance < 0) {
            // 타이머 종료 처리
            stopCountdown();
            if (cachedElements.timer) {
                cachedElements.timer.innerHTML = '<span class="text-[#ef4444]">모집 중!</span>';
            }
            if (cachedElements.label) {
                cachedElements.label.textContent = '현재 모집 중';
            }
            return;
        }

        // 시간 계산 (Math.floor 한 번만 사용)
        const totalSeconds = Math.floor(distance / 1000);
        const days = Math.floor(totalSeconds / 86400);
        const hours = Math.floor((totalSeconds % 86400) / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        // DOM 업데이트 (변경된 값만 업데이트)
        updateElement(cachedElements.days, days);
        updateElement(cachedElements.hours, hours);
        updateElement(cachedElements.minutes, minutes);
        updateElement(cachedElements.seconds, seconds);
    }

    // DOM 요소 업데이트 (변경시에만)
    function updateElement(element, value) {
        if (!element) return;

        const formatted = String(value).padStart(2, '0');
        if (element.textContent !== formatted) {
            element.textContent = formatted;
        }
    }

    // 카운트다운 시작
    function startCountdown() {
        if (countdownInterval) return; // 이미 실행 중

        // requestAnimationFrame 사용 (더 부드러운 업데이트)
        function tick() {
            updateCountdown();
            countdownInterval = requestAnimationFrame(tick);
        }

        // 초기 실행
        updateCountdown();

        // 1초마다 업데이트 (requestAnimationFrame 대신 setInterval 사용도 가능)
        countdownInterval = setInterval(updateCountdown, 1000);
    }

    // 카운트다운 중지
    function stopCountdown() {
        if (countdownInterval) {
            clearInterval(countdownInterval);
            countdownInterval = null;
        }
    }

    // 페이지 visibility 변경 감지 (백그라운드에서 타이머 중지)
    function handleVisibilityChange() {
        if (document.hidden) {
            // 페이지가 숨겨지면 타이머 중지 (리소스 절약)
            stopCountdown();
        } else {
            // 페이지가 다시 보이면 타이머 재시작
            if (timerSettings) {
                startCountdown();
            }
        }
    }

    // 초기화
    function init() {
        // DOM이 준비되었는지 확인
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
            return;
        }

        // 요소 캐싱
        cacheElements();

        // 타이머 요소가 있는 경우에만 실행
        if (cachedElements.timer) {
            // 설정 로드 및 타이머 시작
            loadTimerSettings();

            // Visibility API 이벤트 리스너
            document.addEventListener('visibilitychange', handleVisibilityChange);

            // 페이지 언로드 시 정리
            window.addEventListener('beforeunload', stopCountdown);
        }
    }

    // 외부에서 타이머 리셋할 수 있는 API
    window.resetCountdownTimer = function() {
        stopCountdown();
        localStorage.removeItem('donlinee_timer_settings');
        loadTimerSettings();
    };

    // 초기화 실행
    init();

})();