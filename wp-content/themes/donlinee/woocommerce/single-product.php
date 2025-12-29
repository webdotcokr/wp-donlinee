<?php
/**
 * 전자책 상품 상세 페이지
 *
 * WooCommerce single-product.php 템플릿 오버라이드
 * 전자책 페이지와 동일한 헤더/푸터 사용
 *
 * @package Donlinee
 * @author webdot
 */

defined('ABSPATH') || exit;

// 전자책 전용 헤더 사용
get_header('ebook');

global $product;

// 상품 정보
$product_id = get_the_ID();
$product_title = get_the_title();
$product_price = $product->get_price();
$product_regular_price = $product->get_regular_price();
$product_sale_price = $product->get_sale_price();
$product_description = get_the_content();
$product_short_description = $product->get_short_description();
$product_image = get_the_post_thumbnail_url($product_id, 'full');
$product_rating = $product->get_average_rating();
$product_rating_count = $product->get_rating_count();
$product_gallery = $product->get_gallery_image_ids();

// 기본 이미지 설정
if (!$product_image) {
    $product_image = wc_placeholder_img_src('full');
}

// 가격 포맷팅
$formatted_price = number_format($product_price);
$formatted_regular_price = $product_regular_price ? number_format($product_regular_price) : '';
$is_on_sale = $product->is_on_sale();
?>

<main id="product-main" class="site-main bg-[#f8fafc] min-h-screen">
    <div class="max-w-6xl mx-auto px-6 py-12">

        <!-- 상품 상세 컨테이너 -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

            <!-- 상단: 이미지 + 기본 정보 -->
            <div class="lg:flex">
                <!-- 좌측: 상품 이미지 -->
                <div class="lg:w-1/2 bg-gradient-to-br from-gray-50 to-gray-100">
                    <div class="aspect-square p-8 flex items-center justify-center">
                        <img src="<?php echo esc_url($product_image); ?>"
                             alt="<?php echo esc_attr($product_title); ?>"
                             class="max-w-full max-h-full object-contain rounded-lg shadow-lg">
                    </div>

                    <?php if (!empty($product_gallery)) : ?>
                    <!-- 갤러리 썸네일 -->
                    <div class="px-8 pb-8 flex gap-3 overflow-x-auto">
                        <button class="w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden border-2 border-[#1e3a5f] opacity-100">
                            <img src="<?php echo esc_url($product_image); ?>" alt="" class="w-full h-full object-cover">
                        </button>
                        <?php foreach ($product_gallery as $gallery_id) :
                            $gallery_url = wp_get_attachment_image_url($gallery_id, 'thumbnail');
                        ?>
                        <button class="gallery-thumb w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden border-2 border-transparent hover:border-gray-300 opacity-60 hover:opacity-100 transition-all"
                                data-full="<?php echo esc_url(wp_get_attachment_image_url($gallery_id, 'full')); ?>">
                            <img src="<?php echo esc_url($gallery_url); ?>" alt="" class="w-full h-full object-cover">
                        </button>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- 우측: 상품 정보 -->
                <div class="lg:w-1/2 p-8 lg:p-12 flex flex-col">
                    <!-- 카테고리 -->
                    <?php
                    $categories = get_the_terms($product_id, 'product_cat');
                    if ($categories && !is_wp_error($categories)) :
                    ?>
                    <div class="mb-4">
                        <span class="inline-block bg-[#f0f4f8] text-[#1e3a5f] text-sm font-medium px-3 py-1 rounded-full">
                            <?php echo esc_html($categories[0]->name); ?>
                        </span>
                    </div>
                    <?php endif; ?>

                    <!-- 상품명 -->
                    <h1 class="text-2xl lg:text-3xl font-bold text-[#1e3a5f] mb-4 leading-tight">
                        <?php echo esc_html($product_title); ?>
                    </h1>

                    <!-- 별점 및 리뷰 -->
                    <div class="flex items-center gap-3 mb-6">
                        <?php if ($product_rating > 0) : ?>
                        <div class="flex items-center gap-1">
                            <?php for ($i = 1; $i <= 5; $i++) : ?>
                                <?php if ($i <= floor($product_rating)) : ?>
                                    <span class="text-yellow-400 text-lg">★</span>
                                <?php elseif ($i - 0.5 <= $product_rating) : ?>
                                    <span class="text-yellow-400 text-lg">★</span>
                                <?php else : ?>
                                    <span class="text-gray-300 text-lg">★</span>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <span class="text-gray-700 font-medium"><?php echo number_format($product_rating, 1); ?></span>
                        <span class="text-gray-300">|</span>
                        <span class="text-gray-500">리뷰 <?php echo number_format($product_rating_count); ?>개</span>
                        <?php else : ?>
                        <span class="text-gray-400">아직 리뷰가 없습니다</span>
                        <?php endif; ?>
                    </div>

                    <!-- 간단 설명 -->
                    <?php if ($product_short_description) : ?>
                    <div class="text-gray-600 mb-6 leading-relaxed">
                        <?php echo wp_kses_post($product_short_description); ?>
                    </div>
                    <?php endif; ?>

                    <!-- 구분선 -->
                    <div class="border-t border-gray-100 my-6"></div>

                    <!-- 가격 -->
                    <div class="mb-8">
                        <?php if ($is_on_sale && $formatted_regular_price) : ?>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-gray-400 line-through text-lg"><?php echo $formatted_regular_price; ?>원</span>
                            <span class="bg-red-500 text-white text-sm font-bold px-2 py-1 rounded">
                                <?php echo round((($product_regular_price - $product_price) / $product_regular_price) * 100); ?>% OFF
                            </span>
                        </div>
                        <?php endif; ?>
                        <div class="text-4xl font-bold text-[#1e3a5f]">
                            <?php echo $formatted_price; ?><span class="text-2xl">원</span>
                        </div>
                    </div>

                    <!-- 구매 버튼 -->
                    <div class="mt-auto space-y-3">
                        <?php if ($product->is_purchasable() && $product->is_in_stock()) : ?>
                        <form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
                            <?php do_action('woocommerce_before_add_to_cart_button'); ?>

                            <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>">
                            <input type="hidden" name="quantity" value="1">

                            <button type="submit" class="w-full bg-[#DC2626] hover:bg-[#B91C1C] text-white text-lg font-bold py-4 px-8 rounded-xl transition-colors">
                                구매하기
                            </button>

                            <?php do_action('woocommerce_after_add_to_cart_button'); ?>
                        </form>

                        <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="block w-full text-center border-2 border-[#1e3a5f] text-[#1e3a5f] hover:bg-[#1e3a5f] hover:text-white text-lg font-bold py-4 px-8 rounded-xl transition-colors">
                            장바구니 보기
                        </a>
                        <?php else : ?>
                        <button disabled class="w-full bg-gray-300 text-gray-500 text-lg font-bold py-4 px-8 rounded-xl cursor-not-allowed">
                            품절
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 하단: 상세 설명 -->
            <div class="border-t border-gray-100">
                <!-- 탭 메뉴 -->
                <div class="flex border-b border-gray-100">
                    <button class="product-tab flex-1 py-4 text-center font-semibold text-[#1e3a5f] border-b-2 border-[#1e3a5f] bg-white" data-tab="description">
                        상품 설명
                    </button>
                    <button class="product-tab flex-1 py-4 text-center font-semibold text-gray-400 border-b-2 border-transparent hover:text-gray-600 transition-colors" data-tab="reviews">
                        리뷰 (<?php echo $product_rating_count; ?>)
                    </button>
                </div>

                <!-- 탭 컨텐츠 -->
                <div class="p-8 lg:p-12">
                    <!-- 상품 설명 탭 -->
                    <div id="tab-description" class="product-tab-content">
                        <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                            <?php echo wp_kses_post($product_description); ?>
                        </div>
                    </div>

                    <!-- 리뷰 탭 -->
                    <div id="tab-reviews" class="product-tab-content hidden">
                        <?php
                        // 리뷰 목록
                        $reviews = get_comments(array(
                            'post_id' => $product_id,
                            'status' => 'approve',
                            'type' => 'review',
                        ));

                        if ($reviews) :
                        ?>
                        <div class="space-y-6">
                            <?php foreach ($reviews as $review) :
                                $rating = get_comment_meta($review->comment_ID, 'rating', true);
                            ?>
                            <div class="border-b border-gray-100 pb-6 last:border-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="flex">
                                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                                            <span class="<?php echo $i <= $rating ? 'text-yellow-400' : 'text-gray-300'; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="font-medium text-gray-900"><?php echo esc_html($review->comment_author); ?></span>
                                    <span class="text-gray-400 text-sm"><?php echo date('Y.m.d', strtotime($review->comment_date)); ?></span>
                                </div>
                                <p class="text-gray-600"><?php echo esc_html($review->comment_content); ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else : ?>
                        <div class="text-center py-12">
                            <p class="text-gray-400">아직 작성된 리뷰가 없습니다.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- 목록으로 돌아가기 -->
        <div class="mt-8 text-center">
            <a href="/ebook" class="inline-flex items-center gap-2 text-gray-500 hover:text-[#1e3a5f] transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                <span>목록으로 돌아가기</span>
            </a>
        </div>
    </div>
</main>

<style>
/* 상품 상세 페이지 전용 스타일 */
#product-main .prose h1,
#product-main .prose h2,
#product-main .prose h3,
#product-main .prose h4 {
    color: #1e3a5f;
    font-weight: 700;
    margin-top: 2em;
    margin-bottom: 1em;
}

#product-main .prose h2 {
    font-size: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e5e7eb;
}

#product-main .prose h3 {
    font-size: 1.25rem;
}

#product-main .prose p {
    margin-bottom: 1.5em;
    line-height: 1.8;
}

#product-main .prose ul,
#product-main .prose ol {
    margin: 1.5em 0;
    padding-left: 1.5em;
}

#product-main .prose li {
    margin-bottom: 0.5em;
    line-height: 1.8;
}

#product-main .prose strong {
    color: #1e3a5f;
    font-weight: 600;
}

#product-main .prose img {
    border-radius: 0.5rem;
    margin: 2em 0;
}

/* 반응형 */
@media (max-width: 1024px) {
    #product-main .lg\:flex {
        display: block;
    }

    #product-main .lg\:w-1\/2 {
        width: 100%;
    }

    #product-main .aspect-square {
        aspect-ratio: auto;
        padding: 2rem;
    }
}

@media (max-width: 640px) {
    #product-main .text-4xl {
        font-size: 2rem;
    }

    #product-main .text-2xl {
        font-size: 1.25rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 탭 전환
    const tabs = document.querySelectorAll('.product-tab');
    const tabContents = document.querySelectorAll('.product-tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.dataset.tab;

            // 탭 버튼 스타일 업데이트
            tabs.forEach(t => {
                t.classList.remove('text-[#1e3a5f]', 'border-[#1e3a5f]');
                t.classList.add('text-gray-400', 'border-transparent');
            });
            this.classList.remove('text-gray-400', 'border-transparent');
            this.classList.add('text-[#1e3a5f]', 'border-[#1e3a5f]');

            // 탭 컨텐츠 전환
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById('tab-' + targetTab).classList.remove('hidden');
        });
    });

    // 갤러리 이미지 전환
    const galleryThumbs = document.querySelectorAll('.gallery-thumb');
    const mainImage = document.querySelector('#product-main .aspect-square img');

    galleryThumbs.forEach(thumb => {
        thumb.addEventListener('click', function() {
            const fullUrl = this.dataset.full;
            mainImage.src = fullUrl;

            // 썸네일 스타일 업데이트
            document.querySelectorAll('#product-main .aspect-square + div button').forEach(btn => {
                btn.classList.remove('border-[#1e3a5f]', 'opacity-100');
                btn.classList.add('border-transparent', 'opacity-60');
            });
            this.classList.remove('border-transparent', 'opacity-60');
            this.classList.add('border-[#1e3a5f]', 'opacity-100');
        });
    });
});
</script>

<?php
// 전자책 전용 푸터 사용
get_footer('ebook');
?>
