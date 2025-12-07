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
            <img src="/wp-content/uploads/2025/12/main-1.webp">
            <h3 class="button p text-center">1개월 현금 매출(크몽, 카드, 세금계산서 미발행 매출 미포함)</h3>
        </div>
        <div class="flex flex-col gap-[30px]">
            <img src="/wp-content/uploads/2025/12/main-1.webp">
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
    <section class="py-[80px] flex flex-col gap-[80px]">
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

        <!-- 강의 미리보기 섹션 -->
        <?php
        // 미리보기 강의 데이터
        $previewLectures = array(
            array(
                'chapter' => 'CHAPTER 1',
                'title' => '사업의 기본',
                'lessons' => array(
                    array('title' => '사업으로 인생을 바꾸는데 걸리는 시간', 'videoUrl' => 'https://player.vimeo.com/video/1074857904', 'isPreview' => true, 'duration' => '22:36'),
                    array('title' => '노력으로 성공이 가능할까?', 'videoUrl' => 'https://player.vimeo.com/video/1074858609', 'isPreview' => true, 'duration' => '07:03'),
                    array('title' => '사업이 꼭 정답은 아닌 이유', 'videoUrl' => '', 'isPreview' => false, 'duration' => '07:05'),
                    array('title' => '사업이란 대체 무엇일까', 'videoUrl' => '', 'isPreview' => false, 'duration' => '12:41'),
                    array('title' => '당신이 사업을 어렵게 느끼는 이유', 'videoUrl' => '', 'isPreview' => false, 'duration' => '21:24'),
                    array('title' => '사업을 잘하는 사람들의 특징과 그들이 되는 방법', 'videoUrl' => '', 'isPreview' => false, 'duration' => '09:49'),
                    array('title' => '사업을 운이라고 치부하는 사람들에게 반박', 'videoUrl' => '', 'isPreview' => false, 'duration' => '02:39'),
                    array('title' => '사업은 전략 싸움이다', 'videoUrl' => '', 'isPreview' => false, 'duration' => '20:36'),
                    array('title' => '당신이 책 강의를 봐도 인생이 바뀌지 않는 이유', 'videoUrl' => '', 'isPreview' => false, 'duration' => '09:37'),
                ),
            ),
            array(
                'chapter' => 'CHAPTER 2',
                'title' => '사업 아이템을 정하는 6가지 원칙',
                'lessons' => array(
                    array('title' => '무자본 창업 6가지 방법', 'videoUrl' => 'https://player.vimeo.com/video/1074862903', 'isPreview' => true, 'duration' => '09:37'),
                    array('title' => '1 원칙(비밀)', 'videoUrl' => '', 'isPreview' => false, 'duration' => '10:10'),
                    array('title' => '2 원칙(비밀)', 'videoUrl' => '', 'isPreview' => false, 'duration' => '10:29'),
                    array('title' => '3 원칙(비밀)', 'videoUrl' => '', 'isPreview' => false, 'duration' => '13:59'),
                    array('title' => '4 원칙(비밀)', 'videoUrl' => '', 'isPreview' => false, 'duration' => '10:52'),
                    array('title' => '5 원칙(비밀)', 'videoUrl' => '', 'isPreview' => false, 'duration' => '12:21'),
                    array('title' => '6 원칙(비밀)', 'videoUrl' => '', 'isPreview' => false, 'duration' => '08:34'),
                    array('title' => '좋아하는 일 과연 좋을까? & 잘하는 일 과연 좋을까?', 'videoUrl' => '', 'isPreview' => false, 'duration' => '11:52'),
                    array('title' => '사업 아이템 수백개를 확인하는 벤치마크 사이트', 'videoUrl' => '', 'isPreview' => false, 'duration' => '13:35'),
                ),
            ),
        );
        ?>

        <div id="preview" class="">
            <p class="text-center p mb-8 opacity-90">실제 강의 일부를 미리 경험해보세요</p>

            <div class="space-y-8">
                <?php foreach ($previewLectures as $chapterIndex => $chapterData): ?>
                <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                    <div class="mb-4">
                        <span class="text-sm font-bold text-[#DC2626]"><?php echo $chapterData['chapter']; ?></span>
                        <h5 class="h3 mt-1"><?php echo $chapterData['title']; ?></h5>
                    </div>

                    <div class="space-y-2">
                        <?php foreach ($chapterData['lessons'] as $lessonIndex => $lesson): ?>
                        <div class="preview-accordion-item border border-gray-200 rounded-lg overflow-hidden">
                            <button
                                class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors <?php echo $lesson['isPreview'] ? 'cursor-pointer' : 'cursor-not-allowed opacity-75'; ?>"
                                onclick="toggleAccordion(this, <?php echo $lesson['isPreview'] ? 'true' : 'false'; ?>, '<?php echo addslashes($lesson['videoUrl']); ?>')"
                            >
                                <div class="flex items-center gap-3">
                                    <?php if ($lesson['isPreview']): ?>
                                    <?php else: ?>
                                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                        </svg>
                                    <?php endif; ?>
                                    <span class="text-left font-medium"><?php echo $lesson['title']; ?></span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <?php if ($lesson['isPreview']): ?>
                                        <span class="text-xs bg-[#DC2626] text-white px-2 py-1 rounded font-medium">미리보기</span>
                                    <?php endif; ?>
                                    <span class="text-sm text-gray-500"><?php echo $lesson['duration']; ?></span>
                                    <svg class="w-5 h-5 text-gray-400 accordion-arrow transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>

                            <div class="accordion-content hidden">
                                <?php if ($lesson['isPreview']): ?>
                                    <div class="p-4 bg-gray-50">
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
                                <?php else: ?>
                                    <div class="p-4 bg-gray-50 text-center">
                                        <p class="text-gray-600">🔒 수강 신청 후 모든 강의를 시청하실 수 있습니다.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-8 text-center">
                <p class="p font-medium">
                    <b class="underline">* 강의 목록 중 일부만 공개되었습니다.</b>
                </p>
            </div>
        </div>

        <p>
            이후 강의가 종료되면 수강생이 사업을 시작하도록 매달 온라인을 통해 수강생이 모두 참석하는 세미나를 운영합니다. 사업을 시작하고 매출을 성장시키는 과정까지 1:1로 밀착 관리합니다. <b>그럼에도 매출이 발생하지 않는다면 100% 환불해 드리겠습니다.</b> 그만큼 자신있습니다.<br/><br/>
            제 이전 강의(다마고치, 돈파르타)를 듣고 사업을 시작한 후 매출이 발생하지 않은 경우는 없습니다. 그 어떤 강의보다 성과를 내는 비율이 높다고 자부합니다. 또한 제 강의는 제 구독자가 아님에도 친구의 소개로 지원하는 경우가 많습니다. 먼저 수강한 친구가 추천해주는 방식으로 계속 이어지고 있습니다.
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
            <h3 class="point">6~16주 차</h3>
            <p>노하우 전달 및 무한 피드백 </p>
        </div>
        <p><b>​더 자세한 내용은 아래를 확인해 주세요.</b></p>
    </section>
    <!-- 모집 안내 -->
    <section class="full py-[100px] flex flex-col gap-[60px] text-black bg-[#f1f1f1]">
        <div class="flex flex-col gap-[40px]">
            <h2 class="subTitle !border-black">모집 안내</h2>
            <div class="flex flex-col gap-[20px]">
                <h3 class="point">모집 일자</h3>
                <h4 class="p">2025.12.13 ~ 2025.12.28</h4>
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
                <h3 class="point">비용</h3>
                <h4 class="p">198만원</h4>
            </div>
            <div class="flex flex-col gap-[20px]">
                <h3 class="point">합격자 발표</h3>
                <h4 class="p">2025.12.29 월요일</h4>
            </div>
            <div class="flex flex-col gap-[20px]">
                <h3 class="point">참고</h3>
                <h4 class="p">1. 합격자는 20명입니다. (50명 이상 지원 시 선착순 조기 마감됩니다.) 
                    <br/>2. 유튜브 촬영은 진행되지 않습니다.
                </h4>
            </div>
            <h2 class="px-24 max-md:px-6 py-3 h2 button cursor-pointer">강의 대기 신청하기</h2>
        </div>
    </section>
</main>

<?php get_footer(); ?>
