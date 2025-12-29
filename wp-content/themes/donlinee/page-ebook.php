<?php
/**
 * Template Name: 전자책 판매 페이지
 *
 * PG 심사용 별도 서브페이지
 * 기존 헤더/푸터와 분리된 독립적인 페이지
 *
 * @package Donlinee
 * @author webdot
 */

// 전자책 전용 헤더 사용
get_header('ebook');
?>

<main id="ebook-main" class="site-main bg-[#f8fafc] py-12">
    <div class="max-w-6xl mx-auto px-6">

        <!-- 페이지 제목 -->
        <div class="flex justify-between items-center mb-8" id="products">
            <h1 class="text-2xl font-bold text-[#1e3a5f]">인기 전자책</h1>
            <a href="/ebook" class="text-gray-400 flex items-center gap-2 text-sm hover:text-gray-600 transition-colors">
                <span>&lt;</span>
                <span>모두보기</span>
                <span>&gt;</span>
            </a>
        </div>

        <!-- 전자책 목록 -->
        <div class="ebook-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            // WooCommerce 상품 조회 (전자책 카테고리)
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => 12,
                'orderby' => 'date',
                'order' => 'DESC',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'slug',
                        'terms'    => 'ebook', // 전자책 카테고리 슬러그
                    ),
                ),
            );

            $products = new WP_Query($args);

            if ($products->have_posts()) :
                while ($products->have_posts()) : $products->the_post();
                    global $product;

                    // 상품 정보
                    $product_id = get_the_ID();
                    $product_title = get_the_title();
                    $product_price = $product->get_price();
                    $product_rating = $product->get_average_rating();
                    $product_rating_count = $product->get_rating_count();
                    $product_link = get_permalink();
                    $product_image = get_the_post_thumbnail_url($product_id, 'full');
                    $product_excerpt = get_the_excerpt();

                    // 기본 이미지 설정
                    if (!$product_image) {
                        $product_image = wc_placeholder_img_src();
                    }

                    // 가격 포맷팅
                    $formatted_price = number_format($product_price);
            ?>
                    <a href="<?php echo esc_url($product_link); ?>" class="ebook-card block bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                        <!-- 상단: 텍스트 + 이미지 -->
                        <div class="flex gap-4">
                            <!-- 좌측: 텍스트 정보 -->
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-[#1e3a5f] mb-2 line-clamp-2 leading-tight">
                                    <?php echo esc_html($product_title); ?>
                                </h3>
                                <?php if ($product_excerpt) : ?>
                                <p class="text-sm text-gray-500 line-clamp-2 leading-relaxed">
                                    <?php echo wp_trim_words($product_excerpt, 15, '...'); ?>
                                </p>
                                <?php endif; ?>
                            </div>
                            <!-- 우측: 이미지 + 하트 -->
                            <div class="w-28 h-28 flex-shrink-0 relative">
                                <div class="w-full h-full rounded-xl overflow-hidden bg-gradient-to-br from-yellow-100 to-orange-100">
                                    <img src="<?php echo esc_url($product_image); ?>"
                                         alt="<?php echo esc_attr($product_title); ?>"
                                         class="w-full h-full object-cover">
                                </div>
                                <!-- 하트 아이콘 -->
                                <button type="button" class="absolute top-1 right-1 w-7 h-7 flex items-center justify-center text-blue-300 hover:text-blue-500 transition-colors" onclick="event.preventDefault(); event.stopPropagation();">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- 하단: 메타 정보 -->
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <!-- 상품명 -->
                            <p class="text-sm text-[#1e3a5f] font-medium mb-1 truncate">
                                <?php echo esc_html($product_title); ?>
                            </p>
                            <!-- 별점 및 구매자수 -->
                            <div class="flex items-center gap-2 text-sm mb-2">
                                <?php if ($product_rating > 0) : ?>
                                    <span class="text-yellow-400">★</span>
                                    <span class="text-gray-700 font-medium"><?php echo number_format($product_rating, 1); ?></span>
                                    <span class="text-gray-300">|</span>
                                    <span class="text-gray-500">구매 <?php echo number_format($product_rating_count); ?>명</span>
                                <?php else : ?>
                                    <span class="text-gray-400">리뷰 없음</span>
                                <?php endif; ?>
                            </div>
                            <!-- 가격 -->
                            <p class="text-xl font-bold text-gray-900">
                                <?php echo $formatted_price; ?>원
                            </p>
                        </div>
                    </a>
            <?php
                endwhile;
                wp_reset_postdata();
            else :
            ?>
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">현재 판매 중인 전자책이 없습니다.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<style>
/* 전자책 페이지 전용 스타일 */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* 반응형 디자인 */
@media (max-width: 640px) {
    .ebook-grid {
        gap: 1rem;
    }

    .ebook-card {
        padding: 1rem;
    }

    .ebook-card h3 {
        font-size: 1rem;
    }

    .ebook-card .w-28 {
        width: 6rem;
        height: 6rem;
    }
}
</style>

<?php
// 전자책 전용 푸터 사용
get_footer('ebook');
?>
