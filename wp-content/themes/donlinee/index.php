<?php
/**
 * The main template file
 *
 * @package Donlinee
 * @author webdot
 */

get_header(); ?>

<main id="main" class="site-main">
    <!-- HERO -->
    <section class="full py-[160px] max-md:py-[50px] bg-[#111] text-white">
        <div>
        <h1 class="h1 hero-title">돈마고치<br/>
        4주 안에 월 천만원 벌기</h1>
        <h2 class="mt-[60px] max-md:mt-[30px] h3">
            돈마고치는 국내 최초 강제 실행형 사업 강의입니다. (오프라인 20명 한정)
        </h2>
        </div>
    </section>
    <!-- 강사 인증부터 시작하겠습니다. -->
    <section class="py-[40px] pt-[80px] flex flex-col gap-[100px] max-md:gap-[50px]">
        <h2 class="h2 text-center flex items-center justify-center gap-4 md:gap-6">
            <span>강사 인증</span>
        </h2>
        <div class="flex flex-col gap-[30px]">
            <h2 class="subTitle">크몽 매출 공식 1위</h2>
            <img src="/wp-content/uploads/2025/12/main-1.webp">
            <h3 class="button p text-center">크몽 412만 회원 중 매출 1위</h3>
        </div>
        <div class="flex flex-col gap-[30px]">
            <h2 class="subTitle">1개월 세금 인증</h2>
            <img src="/wp-content/uploads/2025/12/main-2.webp">
            <h3 class="button p text-center">1개월 현금 매출(크몽, 카드, 세금계산서 미발행 매출 미포함)</h3>
        </div>
        <div class="flex flex-col gap-[30px]">
            <img src="/wp-content/uploads/2025/12/main-3-1.webp">
            <h3 class="button p text-center">1개월 사업 비용</h3>
        </div>
        <p><b>제가 현재 운영 중인 디자인 회사의 매출과 세금 자료입니다. </b>
            <br/>위 내용에는 카드, 세금계산서 미발행 매출은 포함되어 있지 않습니다. 해당 금액까지 더하면 매출과 이익은 더 높습니다.
            <br/><br/>위 자료라면 이 글을 보시는 분들께 객관적 인증은 되었다 생각합니다. 실제 강의에서는 제 모든 사업을 공개하고 예시로 설명합니다. <b>만약 위 자료가 거짓인 경우 법적인 책임을 지도록 하겠습니다.<b/></p>
    </section>
    <section class="py-[20px] flex flex-col gap-[80px]">
        <div class="flex flex-col gap-[30px]">
            <h2 class="subTitle">수강생 매출 인증</h2>

            <!-- Swiper 슬라이드 -->
            <div class="swiper testimonial-swiper">
                <div class="swiper-wrapper">
                    <?php for($i = 7; $i <= 20; $i++): ?>
                    <div class="swiper-slide">
                        <img src="/wp-content/uploads/2025/12/<?php echo $i; ?>.webp" alt="수강생 후기 <?php echo $i; ?>">
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="youtube-grid">
                <!-- 래완 유튜브 영상 -->
                <div class="youtube-item">
                    <div class="youtube-embed">
                        <iframe src="https://www.youtube.com/embed/yK1TtQlqrLw"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                        </iframe>
                    </div>
                    <h3 class="h3 text-center">24살이 강의에 300만원 쓰고 나온 충격적인 결과</h3>
                </div>

                <!-- 준하 유튜브 영상 -->
                <div class="youtube-item">
                    <div class="youtube-embed">
                        <iframe src="https://www.youtube.com/embed/egvGgm5OYSY"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                        </iframe>
                    </div>
                    <h3 class="h3 text-center">알바생이 2년만에 사업 성공시킨 비밀</h3>
                </div>

                <!-- 다마고치 유튜브 영상1 -->
                <div class="youtube-item">
                    <div class="youtube-embed">
                        <iframe src="https://www.youtube.com/embed/LAaQEG96IIE"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                        </iframe>
                    </div>
                    <h3 class="h3 text-center">월 천만원, 일반인 참가자 4명으로 3개월 안에 만들기</h3>
                </div>

                <!-- 다마고치 유튜브 영상2 -->
                <div class="youtube-item">
                    <div class="youtube-embed">
                        <iframe src="https://www.youtube.com/embed/QkGcXQoJL3w"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                        </iframe>
                    </div>
                    <h3 class="h3 text-center">매달 1,000만원을 만드는 가장 현실적인 방법</h3>
                </div>
            </div>
            <p class="mt-12 h3 underline text-center"><b>지원 전, 아래 글을 먼저 읽어주세요.</b></p>
            <div class="text-center">
                <a href="/instructor" class="inline-block bg-[#DC2626] text-white px-8 py-4 rounded-lg hover:bg-[#B91C1C] transition-colors font-semibold text-lg">
                    강사소개 보러가기
                </a>
            </div>
        </div>
    </section>
    <!-- 돈마고치 안내 -->
    <section id="program" class="py-[80px] flex flex-col gap-[80px]">
        <div class="flex flex-col gap-[30px]">
            <h2 class="subTitle">돈마고치 안내</h2>
            <p><b>돈마고치는 시중에 존재하는 일반적인 교육이 아닙니다. </b><br/>
            상업적 온라인 강의는 보통 완강률과 실행률이 5%가 채 되지 않습니다. 아마 여러분도 경험으로 잘 알고 계실겁니다.<br/><br/>

            반대로 돈마고치는 사업에 필요한 지식부터 실제 실행까지 강제합니다.<br/>
            과정을 정상적으로 참여한다면 누구나 월 1000만원 이상의 사업체를 가질 수 있도록 설계하였습니다.<br/><br/>

            가장 먼저 <b>4주 간 주말(토,일) 3시간 씩 오프라인을 통해 사업에 대한 모든 지식을 가르칩니다.</b><br/>
            이 과정에서 내용을 완벽히 숙지하였는지 과제를 통해 점검 및 피드백을 진행합니다.
            </p>
        </div>

        <!-- 강의 커리큘럼 섹션 -->
        <?php
        // 전체 커리큘럼 데이터 (17개 챕터)
        $previewLectures = array(
            array(
                'chapter' => 'CHAPTER 1',
                'title' => '사업의 기본',
                'lessons' => array(
                    array('title' => '사업으로 인생을 바꾸는데 걸리는 시간', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '노력으로 성공이 가능할까?', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '사업이 꼭 정답은 아닌 이유', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '사업이란 대체 무엇일까', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '당신이 사업을 어렵게 느끼는 이유', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '사업을 잘하는 사람들의 특징과 그들이 되는 방법', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '사업을 운이라고 치부하는 사람들에게 반박', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '사업은 전략 싸움이다', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '당신이 책, 강의를 봐도 인생이 바뀌지 않는 이유', 'videoUrl' => '', 'isPreview' => false),
                ),
            ),
            array(
                'chapter' => 'CHAPTER 2',
                'title' => '사업 아이템을 정하는 6가지 원칙',
                'lessons' => array(
                    array('title' => '무자본 창업 6가지 방법', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '1 원칙(비밀)', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '2 원칙(비밀)', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '3 원칙(비밀)', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '4 원칙(비밀)', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '5 원칙(비밀)', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '6 원칙(비밀)', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '좋아하는 일, 과연 좋을까?', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '잘하는 일, 과연 좋을까?', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '사업 아이템 수백개를 확인하는 벤치마크 사이트', 'videoUrl' => '', 'isPreview' => false),
                ),
            ),
            array(
                'chapter' => 'CHAPTER 3',
                'title' => '내 상품을 필요하게 만드는 논리적 글쓰기',
                'lessons' => array(
                    array('title' => '고객은 필요할 때만 구매한다', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '고객의 필요를 만드는 주,근,사 법칙', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '글의 구조를 기획하는 3단계', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '문장을 완성하는 3원칙', 'videoUrl' => '', 'isPreview' => false),
                ),
            ),
            array(
                'chapter' => 'CHAPTER 4',
                'title' => '고객의 감성을 설득하는 스토리',
                'lessons' => array(
                    array('title' => '고객은 이성적일까? 감성적일까?', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '고객이 진짜 원하는건 당신의 상품이 아니다', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '스토리를 기획하는 7단계 공식', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '스토리를 언제, 어디서, 어떻게 보여줘야 할까?', 'videoUrl' => '', 'isPreview' => false),
                ),
            ),
            array(
                'chapter' => 'CHAPTER 5',
                'title' => '마케팅의 본질',
                'lessons' => array(
                    array('title' => '사람들이 마케팅을 무서워하는 이유', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '고객은 필요할 때만 구매한다', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '고객이 내 상품을 필요하게 만드는 방법', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '마케팅을 기획하는 4단계 공식', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '4단계를 6단계로 발전 시키기', 'videoUrl' => 'https://player.vimeo.com/video/1144257255', 'isPreview' => true),
                    array('title' => '객단가는 중요하지 않다. 잠재 수익이 핵심이다', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '마케팅은 퍼널이 전부다', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '마케팅 최종 승자의 조건', 'videoUrl' => '', 'isPreview' => false),
                ),
            ),
            array(
                'chapter' => 'CHAPTER 6',
                'title' => '네이버 검색 광고',
                'lessons' => array(
                    array('title' => '네이버 검색 광고 알고리즘 이해', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '광고 효율을 높이는 3가지 전략', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '비용 낭비를 막는 2가지 잔기술', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '효율적인 광고 키워드를 찾는 방법', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '실제 세팅(실습)', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '매출 3배 올리는 보고서 분석', 'videoUrl' => '', 'isPreview' => false),
                ),
            ),
            array(
                'chapter' => 'CHAPTER 7',
                'title' => '구글 검색 광고',
                'lessons' => array(
                    array('title' => '구글 검색 광고 알고리즘 이해', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '구글 광고가 네이버보다 효과적인 이유', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '광고비 5배 뽑는 5가지 전략', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '구글이 꼼수 부리며 우리 돈을 뺏어가는 방법', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '실제 세팅(실습)', 'videoUrl' => '', 'isPreview' => false),
                ),
            ),
            array(
                'chapter' => 'CHAPTER 8',
                'title' => '구글 디스플레이 광고',
                'lessons' => array(
                    array('title' => '구글 디스플레이 광고 알고리즘 이해', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '구글 디스플레이 광고는 최소 10배 효율이 보장된다', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '리타겟팅 광고는 안하면 바보다', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '디스플레이 지면을 공략하는 광고 전략', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '검색어를 공략하는 광고 전략', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '실제 세팅(실습)', 'videoUrl' => '', 'isPreview' => false),
                ),
            ),
            array(
                'chapter' => 'CHAPTER 9',
                'title' => '페이스북/인스타그램 광고',
                'lessons' => array(
                    array('title' => 'META 알고리즘 이해', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '논란의 중심인 이유', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => 'META 광고가 모든 사람에게 효과적인 것은 아니다', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '운이 90% 작용하는 광고 알고리즘', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '운을 극복하는 2가지 전략', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '실제 세팅(실습)', 'videoUrl' => '', 'isPreview' => false),
                ),
            ),
            array(
                'chapter' => 'CHAPTER 10',
                'title' => '블로그 마케팅',
                'lessons' => array(
                    array('title' => '브랜드 블로그와 체험단의 차이', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '네이버 블로그 알고리즘 이해', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '어떤 키워드가 가장 효과적일까? 키워드 추출 방법', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '마케팅 대행사 vs 실행사의 차이', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '대행사와 실행사를 찾는 나만의 방법', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '블로그 광고는 정말 효과적일까? 성과를 측정하는 방법', 'videoUrl' => '', 'isPreview' => false),
                ),
            ),
            array(
                'chapter' => 'CHAPTER 11',
                'title' => '유튜브',
                'lessons' => array(
                    array('title' => '유튜브 알고리즘 이해', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '유튜브의 파급력은 어느정도일까?', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '노출 클릭률 vs 시청 지속 시간', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '유튜브 마케팅의 독특한 깔대기 구조', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '사람들을 구매하게 만드는 동기 이론', 'videoUrl' => '', 'isPreview' => false),
                ),
            ),
            array(
                'chapter' => 'CHAPTER 12',
                'title' => '매출에 따른 적정 마케팅 예산과 전략',
                'lessons' => array(
                    array('title' => '매출이 먼저인가? 마케팅이 먼저인가?', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '월 매출 300만 원을 달성하는 초현실적인 마케팅 예산과 전략', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '월 매출 500만 원을 달성하는 초현실적인 마케팅 예산과 전략', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '월 매출 1000만 원을 달성하는 초현실적인 마케팅 예산과 전략', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '월 매출 3000만 원을 달성하는 초현실적인 마케팅 예산과 전략', 'videoUrl' => '', 'isPreview' => false),
                ),
            ),
            array(
                'chapter' => 'CHAPTER 13',
                'title' => '결국 구매는 웹사이트에서 일어난다',
                'lessons' => array(
                    array('title' => '웹사이트를 기획할 때 가장 중요한 수치 1가지', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '기획 3단계', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '1단계', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '2단계', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '3단계', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '저는 차별점이 없는데요? 있어 보이는 방법', 'videoUrl' => '', 'isPreview' => false),
                ),
            ),
            array(
                'chapter' => 'CHAPTER 14',
                'title' => '가격을 얼마로 해야 할까?',
                'lessons' => array(
                    array('title' => '가격이 오르면 수요가 줄어들까?', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '적정 가격을 정하는 방법', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '가격은 어떻게 올려야 할까?', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '당신도 당하고 있는 가격 전략 3가지', 'videoUrl' => '', 'isPreview' => false),
                ),
            ),
            array(
                'chapter' => 'CHAPTER 15',
                'title' => '회사 이름을 짓는 방법',
                'lessons' => array(
                    array('title' => '3가지 TIP', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '온라인 노출도를 고려해야 한다', 'videoUrl' => '', 'isPreview' => false),
                ),
            ),
            array(
                'chapter' => 'CHAPTER 16',
                'title' => '현실적인 질문들',
                'lessons' => array(
                    array('title' => '동업은 안좋은가요?', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '사업에서 인맥은 중요한가요?', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '순수익은 몇 %가 적당한가요?', 'videoUrl' => '', 'isPreview' => false),
                ),
            ),
            array(
                'chapter' => 'CHAPTER 17',
                'title' => '3년 연속 크몽 수익 1위가 알려주는 크몽 정복기',
                'lessons' => array(
                    array('title' => '크몽 알고리즘에 대한 이해', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '크몽 수수료는 비싼걸까?', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '크몽 마케팅 4원칙', 'videoUrl' => '', 'isPreview' => false),
                    array('title' => '크몽 계정의 유통기한', 'videoUrl' => '', 'isPreview' => false),
                ),
            ),
        );
        ?>

        <div id="preview" class="">
            <h4 class="h2 text-center mb-4">강의 커리큘럼</h4>

            <div class="space-y-4">
                <?php foreach ($previewLectures as $chapterIndex => $chapterData): ?>
                <?php
                    // Chapter 5 (인덱스 4)를 기본적으로 펼친 상태로 설정
                    $isChapter5 = ($chapterIndex === 4);
                ?>
                <div class="chapter-item bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                    <!-- 챕터 헤더 (클릭 시 펼치기/접기) -->
                    <button
                        class="chapter-header w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors cursor-pointer"
                        onclick="toggleChapter(this)"
                    >
                        <div class="flex flex-col items-start">
                            <span class="text-sm font-bold text-[#DC2626]"><?php echo $chapterData['chapter']; ?></span>
                            <h5 class="h3 mt-1 text-left"><?php echo $chapterData['title']; ?></h5>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-500"><?php echo count($chapterData['lessons']); ?>개 레슨</span>
                            <svg class="w-5 h-5 text-gray-400 chapter-arrow transition-transform <?php echo $isChapter5 ? 'rotate' : ''; ?>" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>

                    <!-- 챕터 내용 (레슨 목록) -->
                    <div class="chapter-content <?php echo $isChapter5 ? 'show' : 'hidden'; ?>" <?php echo $isChapter5 ? 'style="display: block;"' : ''; ?>>
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                            <div class="space-y-3">
                                <?php foreach ($chapterData['lessons'] as $lessonIndex => $lesson): ?>
                                <div class="lesson-item flex items-start gap-3 p-3 <?php echo $lesson['isPreview'] ? 'bg-white rounded-lg' : ''; ?>">
                                    <?php if ($lesson['isPreview']): ?>
                                        <svg class="w-5 h-5 text-[#DC2626] flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" />
                                        </svg>
                                    <?php else: ?>
                                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                        </svg>
                                    <?php endif; ?>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-700 font-medium"><?php echo ($lessonIndex + 1) . '. ' . $lesson['title']; ?></span>
                                            <?php if ($lesson['isPreview']): ?>
                                                <span class="text-xs bg-[#DC2626] text-white px-2 py-1 rounded font-medium">미리보기</span>
                                            <?php endif; ?>
                                        </div>

                                        <?php if ($lesson['isPreview'] && $lesson['videoUrl']): ?>
                                        <div class="mt-3">
                                            <div class="video-container relative w-full" style="height: 400px; max-height: 50vh;">
                                                <iframe
                                                    src="<?php echo $lesson['videoUrl']; ?>?badge=0&autopause=0&player_id=0&app_id=58479"
                                                    class="absolute inset-0 w-full h-full"
                                                    frameborder="0"
                                                    allow="autoplay; fullscreen; picture-in-picture"
                                                    allowfullscreen
                                                ></iframe>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <p>
            이후 강의가 종료되면 수강생이 사업을 시작하도록 매달 온라인을 통해 수강생이 모두 참석하는 세미나를 운영합니다. 사업을 시작하고 매출을 성장시키는 과정까지 1:1로 밀착 관리합니다. <b>그럼에도 매출이 발생하지 않는다면 100% 환불해 드리겠습니다.</b> 그만큼 자신있습니다.<br/><br/>
            <b>제 이전 강의(다마고치, 돈파르타)를 듣고 사업을 시작한 후 매출이 발생하지 않은 경우는 없습니다.</b> 그 어떤 강의보다 성과를 내는 비율이 높다고 자부합니다. 또한 제 강의는 제 구독자가 아님에도 친구의 소개로 지원하는 경우가 많습니다. 먼저 수강한 친구가 추천해주는 방식으로 계속 이어지고 있습니다.
        </p>
    </section>
    <!-- 이런 분은 지원하지 마세요. -->
    <section class="full py-[100px] flex flex-col gap-[80px] text-white bg-[#111]">
        <div><h2 class="subTitle">이런 분은 지원하지 마세요.</h2></div>
        <div class="flex flex-col gap-[30px]">
            <h3 class="point">1. 강의를 수강하면 매출은 자동으로 나오겠지?</h3>
            <p>이런 마음가짐으로는 커리큘럼을 정상적으로 수행할 수 없습니다. 저는 직접 해보지 않은 지식은 내 것이 아니라고 생각합니다. 때문에 강의에서는 적극적 참여와 실행을 강제합니다. 시작할 용기가 없다면 처음부터 신청하지 마세요.</p>
        </div>
        <div class="flex flex-col gap-[30px]">
            <h3 class="point">2. 하루 2시간 이상의 시간을 낼 수 없는 사람</h3>
            <p>돈파르타는 1개월 안에 사업에 대한 모든 내용을 배웁니다. 이후 3개월 안에 월 천만원의 매출을 달성하는걸 목표로 하고 있습니다. 이 과정은 결코 쉽지 않습니다. 매일 공부와 과제가 동반됩니다. 이는 하루 최소 2시간을 필요로 합니다.</p>
        </div>
        <div class="flex flex-col gap-[30px]">
            <h3 class="point">3. 간절하지 않은 사람</h3>
            <p>제 강의는 항상 모집 인원보다 지원자 수가 2배 이상 많았습니다. <br/>정말 간절한 사람이 신청해도 떨어질 수 있습니다. 안일한 마음으로 다른 지원자의 기회를 빼앗지 마세요.</p>
        </div>
    </section>
    <!-- 이런 분만 지원해 주세요. -->
    <section class="py-[100px] flex flex-col gap-[80px]">
        <h2 class="subTitle">이런 분만 지원해 주세요.</h2>
        <div class="flex flex-col gap-[30px]">
            <h3 class="point">1. 지금 당장 사업을 하고 싶다. 그런데 뭐부터 해야할 지 모르겠다.</h3>
            <p>돈마고치는 이런 분들을 위한 강의입니다. <br/>시작하기 전 필수 지식, 사업을 시작하는 방법, 매출을 성장시키는 과정을 모두 다루고 있습니다.</p>
        </div>
        <div class="flex flex-col gap-[30px]">
            <h3 class="point">2. 마음은 먹었는데, 용기가 나지 않는다.</h3>
            <p>돈마고치는 강제로 사업을 시작하도록 프로그램이 구성되어 있습니다. <br/>이 과정에서 밀착 점검과 피드백이 이어집니다. 누구나 사업을 시작할 수 있도록 만들어 드립니다.</p>
        </div>
        <div class="flex flex-col gap-[30px]">
            <h3 class="point">3. 무자본으로 사업을 시작하고 싶은 사람</h3>
            <p>돈파르타는 무자본 창업을 다루고 있습니다. 0원으로 무료 마케팅부터 시작하여 매출을 성장시키는 방법론을 배웁니다. <br/>자본이 충분하신 경우라면 저희와 맞지 않습니다.</p>
        </div>
        <p>
        <b>마지막으로 드리고 싶은 말씀이 있습니다.</b><br/><br/>

        입발린 말은 하고 싶지 않습니다. 저는 누구나 월 5000만원, 1억 이상을 벌 수 있다고 생각하지 않습니다. <br/>
        이런 성과는 열심히 한다고 되는게 아닙니다. 노력과 재능이 동시에 필요한 영역입니다.<br/><br/>

        <b>하지만 월 1000만원은 타고난 재능이 필요하지 않습니다. </b><br/>
        분명 노력의 영역입니다. 명확한 길과 방법이 정해져 있습니다. <br/>
        시키는 대로만 한다면 누구나 달성 가능한 영역입니다.
        </p>
    </section>
    <!-- 진행과정 -->
    <section class="py-[40px] flex flex-col gap-[60px]">
        <h2 class="subTitle">진행 과정</h2>
        <div class="flex flex-col gap-[30px]">
            <h3 class="point">1~4주 차</h3>
            <p>주말(토,일) 오후 2시~5시 오프라인 강의</p>
        </div>
        <div class="flex flex-col gap-[30px]">
            <h3 class="point">5주 차</h3>
            <p>강제 사업 시작</p>
        </div>
        <div class="flex flex-col gap-[30px]">
            <h3 class="point">6주 ~ 16주 차</h3>
            <p>노하우 전달 및 무한 피드백</p>
        </div>
        <p><b>​더 자세한 내용은 아래를 확인해 주세요.</b></p>
    </section>

    <!-- FAQ 섹션 -->
    <section class="full py-[100px] flex flex-col gap-[80px] text-black bg-black">
        <div><h2 class="text-white subTitle !border-white text-center">자주 묻는 질문</h2></div

        <!-- FAQ 아이템들 -->
        <div class="max-w-4xl mx-auto w-full px-4 flex flex-col gap-[50px]">

            <!-- Q1: 사업에 정말 공식이 있나요? -->
            <div class="faq-item">
                <div class="bg-[#f8f8f8] rounded-2xl p-8 shadow-sm">
                    <h3 class="text-xl font-bold mb-6 text-center">사업에 정말 공식이 있나요?</h3>
                    <div class="h-px bg-gray-300 mb-6"></div>
                    <div class="leading-relaxed space-y-4">
                        <p><b>네, 사업에는 분명 공식이 존재합니다.</b> 실제로 저 또한 공식을 깨닫고 난 후 디자인 에이전시, 스터디카페, 유튜브, 마케팅 에이전시 등 다양한 사업을 시작하였고 단 한번도 실패한 적이 없습니다. 사업 아이템을 정하는 6가지 기준부터 마케팅 4단계 퍼널까지 분명 정답은 정해져 있습니다.</p>
                        <p>물론 일반 대중 분들은 이 사실을 믿기 어려울 수 있습니다. 제 말에 신뢰도를 높일 수 있도록 <b>30대에 시가총액 8조 기업을 만들어낸 APR 김병훈 대표님의 말씀을 인용</b>하겠습니다.</p>
                        <div class="my-6 text-center">
                            <img src="/wp-content/uploads/2025/12/ref-1.webp" alt="APR 김병훈 대표 인용" class="inline-block max-w-full h-auto rounded-lg shadow-md">
                            <img src="/wp-content/uploads/2025/12/ref-2.webp" alt="APR 김병훈 대표 인용" class="inline-block max-w-full h-auto rounded-lg shadow-md">
                        </div>
                        <p class="text-sm text-gray-600">
                            <a href="https://youtu.be/q_NgaXK0l6c?si=fzCCDgFq_JeDV-zJ&t=148" target="_blank" class="underline">출처: https://www.youtube.com/watch?v=q_NgaXK0l6c</a> <br/>
                            * 영상은 2분 35초부터 시청하시면 됩니다.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Q2: 정말 매출이 발생하지 않으면 환불해 주나요? -->
            <div class="faq-item">
                <div class="bg-[#f8f8f8] rounded-2xl p-8 shadow-sm">
                    <h3 class="text-xl font-bold mb-6 text-center">정말 매출이 발생하지 않으면 환불해 주나요?</h3>
                    <div class="h-px bg-gray-300 mb-6"></div>
                    <div class="leading-relaxed">
                        <p>네, 돈마고치는 정규 과정을 모두 이행하였음에도 <br/><b>수강료 이상의 매출이 발생하지 않는 경우 100% 환불</b>해 드립니다.</p>
                    </div>
                </div>
            </div>

            <!-- Q3: 제가 할 줄 아는게 없습니다. 어떤 사업 아이템 선정을 도와주나요? -->
            <div class="faq-item">
                <div class="bg-[#f8f8f8] rounded-2xl p-8 shadow-sm">
                    <h3 class="text-xl font-bold mb-6 text-center">제가 할 줄 아는게 없습니다. 어떤 사업 아이템 선정을 도와주나요?</h3>
                    <div class="h-px bg-gray-300 mb-6"></div>
                    <div class="leading-relaxed">
                        <p>네, 돈마고치에서는 사업 아이템을 정하는 방법에 대해 자세히 다루고 있습니다. 또한 정하신 사업 아이템에 대한 피드백도 진행합니다. 강의를 도와주시는 코치 분들 또한 이미 좋은 성과를 낸 제 수강생 출신이며, 다양한 아이템의 사업을 운영하고 있습니다.</p>
                    </div>
                </div>
            </div>

            <!-- Q4: 제가 직장을 다니고 있습니다. 병행할 수 있나요? -->
            <div class="faq-item">
                <div class="bg-[#f8f8f8] rounded-2xl p-8 shadow-sm">
                    <h3 class="text-xl font-bold mb-6 text-center">제가 직장을 다니고 있습니다. 병행할 수 있나요?</h3>
                    <div class="h-px bg-gray-300 mb-6"></div>
                    <div class="leading-relaxed">
                        <p>지금까지 제가 진행한 강의의 수강생 중 70% 이상이 대학생 혹은 직장인으로 본업이 있으신 분들이었습니다. 저 또한 본업을 바로 그만두는 것을 권장하지 않습니다. 사업을 함께 병행하며 성과가 난 후 본격적으로 시작해 보는 것을 추천합니다. 다만, 하루의 2~3시간 정도의 시간은 필연적으로 필요합니다.</p>
                    </div>
                </div>
            </div>

            <!-- Q5: 이전에 진행한 다마고치, 돈파르타 강의와 무엇이 다른가요? -->
            <div class="faq-item">
                <div class="bg-[#f8f8f8] rounded-2xl p-8 shadow-sm">
                    <h3 class="text-xl font-bold mb-6 text-center">이전에 진행한 다마고치, 돈파르타 강의와 무엇이 다른가요?</h3>
                    <div class="h-px bg-gray-300 mb-6"></div>
                    <div class="leading-relaxed">
                        <p>제가 처음 진행한 다마고치에서 단점을 보완하여 업그레이드한 강의가 돈파르타였습니다. 이 강의들의 총 수강생 합은 1,000명 이상입니다. 이들의 피드백을 받아 추가로 업그레이드 한 강의가 이번 "돈마고치"입니다. 사업에 대한 기본적인 지식부터 강제 실행, 개별 피드백이 모두 포함되어 있습니다.</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- 모집 안내 -->
    <section class="full py-[100px] flex flex-col gap-[60px] text-black bg-[#f1f1f1]">
        <div class="flex flex-col gap-[40px]">
            <h2 class="subTitle !border-black">모집 안내</h2>
            <div class="flex flex-col gap-[20px]">
                <h3 class="point">모집 일자</h3>
                <h4 class="p">2025.12.13 오전 11시 ~ 2025.12.28</h4>
            </div>
            <div class="flex flex-col gap-[20px]">
                <h3 class="point">진행 위치</h3>
                <h4 class="p">서울 강남역 인근 강의실</h4>
            </div>
            <div class="flex flex-col gap-[20px]">
                <h3 class="point">신청 방법</h3>
                <h4 class="p">홈페이지 내 지원서 작성</h4>
            </div>
            <div class="flex flex-col gap-[20px]">
                <h3 class="point">가격</h3>
                <h4 class="p">198만원</h4>
            </div>
            <div class="flex flex-col gap-[20px]">
                <h3 class="point">합격자 발표</h3>
                <h4 class="p">2025.12.29 월요일</h4>
            </div>
            <div class="flex flex-col gap-[20px]">
                <h3 class="point">참고</h3>
                <h4 class="p">1. <span class="underline font-bold">합격자는 20명</span>입니다. (50명 이상 지원 시 선착순 조기 마감됩니다.)
                    <br/>2. 유튜브 촬영은 진행되지 않습니다.</h4>
            </div>
            <a href="#" class="donlinee-waitlist-trigger text-center bg-[#DC2626] text-white px-12 py-6 rounded-lg hover:bg-[#B91C1C] transition-colors font-bold text-xl shadow-lg">
                <span>수강 대기 신청하기</span> →
            </a>
        </div>
</main>

<?php get_footer(); ?>