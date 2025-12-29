<?php
/**
 * 전자책 판매 페이지 전용 헤더
 * PG 심사를 위한 심플한 헤더 (로고 + CTA 버튼만)
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
  <meta name="viewport" content="width=device-width, user-scalable=no">
  <title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>
  <meta name="description" content="<?php bloginfo('description'); ?>">
  <meta name="author" content="홈페이지제작업체 - 웹닷">
  <meta name="publisher" content="홈페이지제작업체 - 웹닷">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Wanted+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://webfontworld.github.io/gmarket/GmarketSans.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>">

  <style>
    /* 전자책 페이지 전용 스타일 */
    body {
      padding-top: 80px;
    }

    @media (max-width: 768px) {
      body {
        padding-top: 60px;
      }
    }

    #ebook-header {
      height: 80px;
      background-color: #ffffff;
      border-bottom: 1px solid #e5e7eb;
      box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
      top: 0;
      left: 0;
      right: 0;
    }

    @media (max-width: 768px) {
      #ebook-header {
        height: 60px;
      }
    }
  </style>

  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

  <!-- 전자책 페이지 전용 헤더 (심플 버전) -->
  <header id="ebook-header" class="w-full z-30 fixed bg-white flex items-center">
    <div class="flex justify-between w-full max-w-6xl mx-auto items-center px-6 max-md:px-3">
      <a href="/"><img src="/wp-content/uploads/2025/12/logo.png" class="w-[110px] max-md:w-[70px]" alt="돈린이 로고"></a>
      <nav>
        <a href="/ebook#products" class="bg-[#DC2626] text-white px-6 py-2 max-md:px-3 max-md:py-1 rounded hover:bg-[#B91C1C] transition-colors font-semibold max-md:text-xs inline-block">
          구매하기
        </a>
      </nav>
    </div>
  </header>
