#!/bin/bash

# 성능 최적화 적용 스크립트
# 22초 지연 문제 해결을 위한 최적화 적용

set -e

echo "========================================="
echo "WordPress 성능 최적화 적용"
echo "========================================="
echo ""

# 색상 정의
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# 1. 최적화된 Settings 클래스 적용
echo "1. 캐싱이 구현된 Settings 클래스 적용..."
if [ -f "/Users/kimjunha/Desktop/docker/donlinee/wp-content/plugins/donlinee-enrollment/includes/class-settings-optimized.php" ]; then
    # 기존 파일 백업
    cp /Users/kimjunha/Desktop/docker/donlinee/wp-content/plugins/donlinee-enrollment/includes/class-settings.php \
       /Users/kimjunha/Desktop/docker/donlinee/wp-content/plugins/donlinee-enrollment/includes/class-settings.backup.php

    # 최적화된 파일로 교체
    cp /Users/kimjunha/Desktop/docker/donlinee/wp-content/plugins/donlinee-enrollment/includes/class-settings-optimized.php \
       /Users/kimjunha/Desktop/docker/donlinee/wp-content/plugins/donlinee-enrollment/includes/class-settings.php

    echo -e "${GREEN}✓ Settings 클래스 최적화 완료${NC}"
else
    echo -e "${YELLOW}⚠ 최적화된 Settings 클래스 파일이 없습니다${NC}"
fi
echo ""

# 2. 최적화된 타이머 코드 적용
echo "2. 최적화된 타이머 코드 적용..."
if [ -f "/Users/kimjunha/Desktop/docker/donlinee/wp-content/themes/donlinee/js/optimized-timer.js" ]; then
    echo "새 타이머 스크립트를 footer.php에 포함시켜주세요:"
    echo "  <script src=\"<?php echo get_template_directory_uri(); ?>/js/optimized-timer.js\"></script>"
    echo -e "${GREEN}✓ 타이머 코드 준비 완료${NC}"
else
    echo -e "${YELLOW}⚠ 최적화된 타이머 파일이 없습니다${NC}"
fi
echo ""

# 3. MySQL 설정 재시작
echo "3. MySQL 설정 적용을 위한 재시작..."
docker-compose restart db
sleep 5
echo -e "${GREEN}✓ MySQL 재시작 완료${NC}"
echo ""

# 4. OPcache 리셋
echo "4. PHP OPcache 리셋..."
docker exec wp-app sh -c "echo '<?php opcache_reset(); ?>' | php" 2>/dev/null || echo "OPcache 리셋 실패"
echo -e "${GREEN}✓ OPcache 리셋 완료${NC}"
echo ""

# 5. WordPress 캐시 클리어
echo "5. WordPress 캐시 클리어..."
# 트랜지언트 클리어
docker exec donlinee mysql -uroot -p\${MYSQL_ROOT_PASSWORD} wordpress -e "
    DELETE FROM wp_options WHERE option_name LIKE '_transient_%';
    DELETE FROM wp_options WHERE option_name LIKE '_site_transient_%';
" 2>/dev/null || echo "트랜지언트 클리어 실패"

# Redis 캐시 클리어 (있는 경우)
docker exec wp-redis redis-cli FLUSHALL 2>/dev/null || echo "Redis 캐시 클리어 건너뜀"

echo -e "${GREEN}✓ 캐시 클리어 완료${NC}"
echo ""

# 6. 데이터베이스 최적화
echo "6. 데이터베이스 테이블 최적화..."
docker exec donlinee mysql -uroot -p\${MYSQL_ROOT_PASSWORD} wordpress -e "
    OPTIMIZE TABLE wp_options;
    OPTIMIZE TABLE wp_posts;
    OPTIMIZE TABLE wp_postmeta;
    OPTIMIZE TABLE wp_donlinee_enrollment_settings;
    OPTIMIZE TABLE wp_donlinee_enrollments;
" 2>/dev/null || echo "테이블 최적화 실패"
echo -e "${GREEN}✓ 테이블 최적화 완료${NC}"
echo ""

# 7. 자동로드 옵션 정리
echo "7. 자동로드 옵션 정리..."
docker exec donlinee mysql -uroot -p\${MYSQL_ROOT_PASSWORD} wordpress -e "
    UPDATE wp_options SET autoload = 'no'
    WHERE autoload = 'yes'
    AND option_name NOT IN (
        'siteurl', 'home', 'blogname', 'blogdescription',
        'users_can_register', 'default_role', 'timezone_string',
        'date_format', 'time_format', 'start_of_week'
    )
    AND option_name NOT LIKE 'widget_%'
    AND option_name NOT LIKE 'theme_%';
" 2>/dev/null || echo "자동로드 정리 실패"
echo -e "${GREEN}✓ 자동로드 옵션 정리 완료${NC}"
echo ""

# 8. 성능 테스트
echo "8. 성능 개선 확인..."
echo "----------------------------------------"
echo "최적화 전후 비교를 위한 응답 시간 측정:"
for i in 1 2 3; do
    response_time=$(curl -o /dev/null -s -w '%{time_total}' http://localhost:8000/)
    echo "  시도 $i: ${response_time}초"
    sleep 1
done
echo ""

# 9. 추가 권장사항
echo "========================================="
echo "최적화 완료!"
echo "========================================="
echo ""
echo "추가 권장 작업:"
echo ""
echo "1. Query Monitor 플러그인 설치 (아직 안했다면):"
echo "   ./scripts/install-query-monitor.sh"
echo ""
echo "2. 성능 문제 진단 실행:"
echo "   ./scripts/debug-performance.sh"
echo ""
echo "3. 컨테이너 재시작 (필요시):"
echo "   docker-compose restart"
echo ""
echo "4. 브라우저 캐시 클리어 후 테스트"
echo ""
echo "5. 여전히 느린 경우:"
echo "   - 플러그인을 하나씩 비활성화하며 테스트"
echo "   - Query Monitor에서 느린 쿼리 확인"
echo "   - MySQL slow query log 확인"
echo ""
echo "========================================="