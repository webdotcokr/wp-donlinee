<?php
/**
 * The header template file
 *
 * @package Donlinee
 * @author webdot
 */
?>
<!DOCTYPE html>
<html lang="ko">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title></title>
  <meta name="description" content="">
  <meta name="author" content="홈페이지제작업체 - 웹닷">
  <meta name="publisher" content="홈페이지제작업체 - 웹닷">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Wanted+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://webfontworld.github.io/gmarket/GmarketSans.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css"/>
  <link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>">
  <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
  <header id="header" class="w-full z-30 fixed bg-white flex items-center border-b border-gray-200 shadow-sm">
    <div class="flex justify-between w-full max-w-6xl mx-auto items-center px-6">
      <a href="/"><img src="/wp-content/uploads/2025/12/logo.png" class="w-[90px] max-md:w-[60px]"></a>
      <nav class="max-lg:hidden">
        <ol class="flex items-center gap-12 font-medium text-black">
          <li><a href="/instructor" class="hover:text-gray-600 transition-colors">강사소개</a></li>
          <li><a href="/service" class="hover:text-gray-600 transition-colors">돈마고치 안내</a></li>
          <li><a href="/contact"><button class="bg-[#DC2626] text-white px-6 py-2 rounded hover:bg-[#B91C1C] transition-colors font-semibold">수강 대기 신청</button></a></li>
        </ol>
      </nav>
      <div class="flex lg:hidden">
        <img src="/wp-content/uploads/2025/09/hamburger.svg" id="hamburger" class="w-6 h-6 cursor-pointer">
        <img src="/wp-content/uploads/2025/09/close.svg" id="close" class="hidden w-6 h-6 cursor-pointer">
      </div>
    </div>
  </header>

  <!-- mobile GNB Overlay -->
   <div id="gnb" class=" container flex flex-col justify-center items-center w-screen h-screen bg-black/95 z-20 fixed text-white list-none
   font-bold hidden text-center
   ">
      <a href="/about" class="w-full"><li class="text-[25px] py-8 border-b border-gray-800">회사소개</li></a>
      <a href="/service" class="w-full"><li class="text-[25px] py-8 border-b border-gray-800">가격안내</li></a>
      <a href="/column" class="w-full"><li class="text-[25px] py-8 border-b border-gray-800">전문칼럼</li></a>
      <a href="/notice" class="w-full"><li class="text-[25px] py-8 border-b border-gray-800">공지</li></a>
      <a href="/faq" class="w-full"><li class="text-[25px] py-8 border-b border-gray-800">FAQ</li></a>
      <a href="/contact" class="w-full"><li class="text-[25px] py-8 border-b border-gray-800">무료 컨설팅 받기</li></a>
   </div>