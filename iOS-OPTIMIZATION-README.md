# iOS WebKit 성능 최적화 가이드

## 🎯 문제 상황
- **증상**: iOS Safari/Chrome에서만 심각한 성능 저하 (22초 이상 지연)
- **원인**: iOS WebKit 엔진과 특정 JavaScript/CSS 조합의 충돌
- **영향 범위**: iPhone, iPad의 모든 브라우저 (Safari, Chrome, Firefox 등)

## 📋 해결 방안 구현 완료

### 1. **생성된 파일들**

```
/wp-content/themes/donlinee/
├── js/ios-optimization.js          # iOS 최적화 JavaScript
├── css/ios-optimization.css        # iOS 전용 CSS
├── footer-optimized.php            # 최적화된 footer 파일
├── header-ios-snippet.php          # header에 추가할 코드
└── ios-performance-test.html       # 성능 테스트 페이지
```

## 🚀 적용 방법

### Step 1: Header.php 수정

`header.php`의 `</head>` 태그 직전에 다음 추가:

```php
<!-- iOS 최적화 -->
<?php include get_template_directory() . '/header-ios-snippet.php'; ?>
</head>
```

### Step 2: Footer.php 교체

**옵션 A: 전체 교체 (권장)**
```bash
# 백업
cp footer.php footer-backup.php

# 최적화 버전으로 교체
cp footer-optimized.php footer.php
```

**옵션 B: 부분 수정**
1. Swiper 초기화 코드에 iOS 조건 추가
2. 스크롤 이벤트에 throttling 적용
3. 타이머 코드에 visibility API 추가

### Step 3: 캐시 클리어

```bash
# WordPress 캐시 클리어
docker exec wp-app wp cache flush --path=/var/www/html --allow-root

# Redis 캐시 클리어 (있는 경우)
docker exec wp-redis redis-cli FLUSHALL

# OPcache 리셋
docker exec wp-app sh -c "echo '<?php opcache_reset(); ?>' | php"
```

## 🧪 테스트 방법

### 1. iOS 실제 디바이스 테스트

1. iPhone/iPad에서 사이트 접속
2. 성능 개선 확인:
   - 페이지 로드 시간
   - 스크롤 부드러움
   - 애니메이션 버벅임

### 2. 성능 테스트 페이지

```
http://localhost:8000/ios-performance-test.html
```

이 페이지에서 확인 가능한 항목:
- FPS 실시간 모니터링
- 디바이스 정보
- 최적화 적용 여부
- 성능 벤치마크

### 3. Safari 개발자 도구 (Mac 필요)

1. iPhone: 설정 > Safari > 고급 > 웹 검사기 활성화
2. Mac Safari: 개발자 메뉴 표시 활성화
3. iPhone을 Mac에 연결
4. Mac Safari: 개발 > [iPhone 이름] > 사이트 선택
5. Timeline 탭에서 성능 프로파일링

## 📊 최적화 내용 상세

### JavaScript 최적화
- ✅ Swiper.js autoplay 간격 조정 (0ms → 5000ms)
- ✅ 스크롤 이벤트 throttling (16ms)
- ✅ requestAnimationFrame 최적화
- ✅ Passive 이벤트 리스너 사용
- ✅ Page Visibility API 활용

### CSS 최적화
- ✅ 무한 애니메이션 비활성화/속도 조절
- ✅ Transform 제거 (고정 배너)
- ✅ GPU 가속 힌트 추가
- ✅ Box-shadow 단순화
- ✅ Backdrop-filter 제거

### 메모리 관리
- ✅ 백그라운드에서 타이머 중지
- ✅ 이미지 lazy loading
- ✅ 불필요한 애니메이션 일시정지

## 🎯 예상 성능 개선

| 항목 | 개선 전 | 개선 후 | 개선율 |
|------|---------|---------|--------|
| 페이지 로드 | 22.24초 | 3-5초 | 80% ↓ |
| FPS | 15-20 | 55-60 | 200% ↑ |
| 스크롤 지연 | 심각 | 부드러움 | 90% ↓ |
| 배터리 소모 | 높음 | 보통 | 40% ↓ |

## ⚠️ 주의사항

1. **캐시 문제**
   - 변경 후 반드시 브라우저 캐시 클리어
   - iOS Safari: 설정 > Safari > 방문 기록 및 웹 사이트 데이터 지우기

2. **플러그인 충돌**
   - WP Fastest Cache 등 캐시 플러그인 설정 확인
   - Minification 비활성화 권장

3. **테스트 환경**
   - 실제 iOS 디바이스에서 테스트 필수
   - iOS 시뮬레이터는 정확하지 않음

## 🔧 추가 최적화 옵션

### 더 공격적인 최적화가 필요한 경우

1. **모든 애니메이션 제거**
```css
.ios-device * {
    animation: none !important;
    transition: none !important;
}
```

2. **Swiper 완전 비활성화**
```javascript
if (isIOS) {
    // Swiper 대신 일반 스크롤 사용
    $('.testimonial-swiper').css('overflow-x', 'auto');
}
```

3. **동적 콘텐츠 지연 로딩**
```javascript
if (isIOS) {
    // Intersection Observer로 뷰포트 진입 시 로드
}
```

## 📞 문제 해결

여전히 느린 경우:

1. **Query Monitor 플러그인 설치**
   ```bash
   ./scripts/install-query-monitor.sh
   ```

2. **성능 진단 실행**
   ```bash
   ./scripts/debug-performance.sh
   ```

3. **특정 플러그인 비활성화**
   - WooCommerce
   - Yoast SEO
   - 기타 무거운 플러그인

## ✅ 체크리스트

- [ ] header.php에 iOS 최적화 코드 추가
- [ ] footer.php를 최적화 버전으로 교체
- [ ] 캐시 모두 클리어
- [ ] 실제 iOS 디바이스에서 테스트
- [ ] FPS 60 근처 유지 확인
- [ ] 스크롤 부드러움 확인
- [ ] 배터리 소모 확인

## 📈 모니터링

적용 후 지속적인 모니터링:

1. Google Analytics에서 iOS 사용자 이탈률 확인
2. 페이지 로드 시간 추적
3. 사용자 피드백 수집

---

**문제가 지속되면 iOS 버전별 세부 최적화가 필요할 수 있습니다.**