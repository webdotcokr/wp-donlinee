<?php
/**
 * Header.php에 추가할 iOS 최적화 코드 스니펫
 * 이 코드를 header.php의 <head> 섹션 끝부분(</head> 태그 직전)에 추가하세요
 */
?>

<!-- iOS WebKit 성능 최적화 -->
<script>
// iOS 조기 감지 및 클래스 추가
(function() {
    var isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    if (isIOS) {
        document.documentElement.className += ' ios-device';
        var version = (navigator.userAgent.match(/OS (\d+)_/) || [])[1];
        if (version) {
            document.documentElement.className += ' ios-' + version;
        }
    }
})();
</script>

<!-- iOS 최적화 CSS -->
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/ios-optimization.css">

<!-- iOS에서만 특정 스타일 적용 -->
<style>
@supports (-webkit-touch-callout: none) {
    /* iOS 전용 긴급 최적화 */
    .animate-scroll {
        animation-duration: 40s !important; /* 속도 감소 */
    }

    /* 고정 배너 transform 제거 */
    #fixed-banner {
        transform: none !important;
        left: 0 !important;
        right: 0 !important;
        margin: 0 auto !important;
    }

    /* 스크롤 최적화 */
    body, html {
        -webkit-overflow-scrolling: touch !important;
    }
}
</style>

<!-- 성능 모니터링 (개발 모드에서만) -->
<?php if (WP_DEBUG): ?>
<script>
console.log('iOS Optimization: Page load started');
window.addEventListener('load', function() {
    if (window.performance && window.performance.timing) {
        var loadTime = window.performance.timing.loadEventEnd - window.performance.timing.navigationStart;
        console.log('Page Load Time:', loadTime + 'ms');

        // iOS 디바이스에서만 성능 경고
        if (/iPad|iPhone|iPod/.test(navigator.userAgent) && loadTime > 3000) {
            console.warn('iOS Performance Warning: Page load took', loadTime + 'ms');
        }
    }
});
</script>
<?php endif; ?>