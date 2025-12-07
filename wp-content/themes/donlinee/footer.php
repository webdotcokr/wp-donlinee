<footer>
<div class="py-8 text-[#8a8a8a] text-[13px]">
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
<div id="fixed-banner" class="fixed left-1/2 transform -translate-x-1/2 z-50 w-full lg:w-[60vw] px-4 lg:px-0" style="bottom: 80px; display: none;">
    <div class="bg-[#3a3a3a] text-white rounded-lg shadow-lg">
        <div class="px-4 sm:px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- 배너 내용 -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-6">
                    <span class="text-sm sm:text-base font-medium">
                        비즈니스 PT 수익화 트레이닝, 지금 신청하세요!
                        <span class="text-xs sm:text-sm opacity-90">(선착 후 불만족 시 100% 환불)</span>
                    </span>
                    <a href="/대기신청" class="inline-block bg-[#ef4444] hover:bg-[#dc2626] text-white px-6 py-2 rounded text-sm font-medium transition-colors duration-200">
                        대기신청
                    </a>
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

        // 배너 항상 표시
        if (banner) {
            banner.style.display = 'block';
        }
    });
  </script>
</body>

</html>