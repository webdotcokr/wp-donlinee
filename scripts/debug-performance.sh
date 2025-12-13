#!/bin/bash

# WordPress 성능 문제 디버깅 스크립트
# 22초 지연 문제의 원인을 찾기 위한 종합 진단

set -e

echo "========================================="
echo "WordPress 성능 문제 진단 스크립트"
echo "========================================="
echo ""

# 색상 정의
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# 1. 컨테이너 상태 확인
echo "1. Docker 컨테이너 상태 확인..."
echo "----------------------------------------"
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
echo ""

# 2. 리소스 사용량 확인
echo "2. 컨테이너 리소스 사용량..."
echo "----------------------------------------"
docker stats --no-stream --format "table {{.Container}}\t{{.Name}}\t{{.CPUPerc}}\t{{.MemUsage}}\t{{.MemPerc}}"
echo ""

# 3. MySQL 느린 쿼리 확인
echo "3. MySQL 느린 쿼리 확인 (최근 10개)..."
echo "----------------------------------------"
docker exec donlinee sh -c "tail -n 50 /var/log/mysql/slow.log 2>/dev/null || echo '느린 쿼리 로그 없음'"
echo ""

# 4. MySQL 현재 프로세스 목록
echo "4. MySQL 현재 실행 중인 쿼리..."
echo "----------------------------------------"
docker exec donlinee mysql -uroot -p\${MYSQL_ROOT_PASSWORD} -e "SHOW FULL PROCESSLIST;" 2>/dev/null || echo "MySQL 접속 실패"
echo ""

# 5. MySQL 상태 정보
echo "5. MySQL 성능 지표..."
echo "----------------------------------------"
docker exec donlinee mysql -uroot -p\${MYSQL_ROOT_PASSWORD} -e "
    SHOW GLOBAL STATUS WHERE Variable_name IN (
        'Slow_queries',
        'Threads_connected',
        'Threads_running',
        'Questions',
        'Com_select',
        'Com_insert',
        'Com_update',
        'Com_delete',
        'Innodb_buffer_pool_read_requests',
        'Innodb_buffer_pool_reads',
        'Table_locks_waited',
        'Created_tmp_disk_tables'
    );
" 2>/dev/null || echo "MySQL 상태 조회 실패"
echo ""

# 6. PHP 에러 로그 확인
echo "6. PHP 에러 로그 (최근 20줄)..."
echo "----------------------------------------"
docker exec wp-app sh -c "tail -n 20 /var/log/php/error.log 2>/dev/null || echo 'PHP 에러 로그 없음'"
echo ""

# 7. WordPress 디버그 로그
echo "7. WordPress 디버그 로그 (최근 20줄)..."
echo "----------------------------------------"
docker exec wp-app sh -c "tail -n 20 /var/www/html/wp-content/debug.log 2>/dev/null || echo 'WordPress 디버그 로그 없음'"
echo ""

# 8. 활성 플러그인 목록
echo "8. 활성화된 WordPress 플러그인..."
echo "----------------------------------------"
docker exec wp-app sh -c "
    if command -v wp &> /dev/null; then
        wp plugin list --status=active --path=/var/www/html --allow-root 2>/dev/null
    else
        echo 'WP-CLI가 설치되지 않음'
    fi
"
echo ""

# 9. 페이지 로드 시간 테스트
echo "9. 페이지 로드 시간 측정..."
echo "----------------------------------------"
echo "홈페이지 응답 시간 (3회 측정):"
for i in 1 2 3; do
    response_time=$(curl -o /dev/null -s -w '%{time_total}' http://localhost:8000/)
    echo "  시도 $i: ${response_time}초"
    sleep 1
done
echo ""

# 10. Nginx 캐시 상태 (있는 경우)
echo "10. Nginx 캐시 상태..."
echo "----------------------------------------"
docker exec wp-nginx sh -c "ls -la /var/cache/nginx/ 2>/dev/null | head -20" 2>/dev/null || echo "Nginx 캐시 디렉토리 없음"
echo ""

# 11. 데이터베이스 테이블 크기
echo "11. WordPress 데이터베이스 테이블 크기..."
echo "----------------------------------------"
docker exec donlinee mysql -uroot -p\${MYSQL_ROOT_PASSWORD} -e "
    SELECT
        table_name AS 'Table',
        ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
    FROM information_schema.TABLES
    WHERE table_schema = 'wordpress'
    ORDER BY (data_length + index_length) DESC
    LIMIT 10;
" 2>/dev/null || echo "테이블 크기 조회 실패"
echo ""

# 12. 권장사항 출력
echo "========================================="
echo "진단 결과 및 권장사항"
echo "========================================="

# 느린 쿼리 확인
slow_queries=$(docker exec donlinee mysql -uroot -p\${MYSQL_ROOT_PASSWORD} -N -e "SHOW GLOBAL STATUS LIKE 'Slow_queries';" 2>/dev/null | awk '{print $2}')
if [ -n "$slow_queries" ] && [ "$slow_queries" -gt 0 ]; then
    echo -e "${RED}⚠ 느린 쿼리 발견: ${slow_queries}개${NC}"
    echo "  → Query Monitor 플러그인을 설치하여 상세 분석 필요"
    echo "  → ./scripts/install-query-monitor.sh 실행"
else
    echo -e "${GREEN}✓ 느린 쿼리 없음${NC}"
fi

# 메모리 사용률 확인
mem_percent=$(docker stats --no-stream --format "{{.MemPerc}}" wp-app | sed 's/%//')
if (( $(echo "$mem_percent > 80" | bc -l) )); then
    echo -e "${RED}⚠ PHP 메모리 사용률 높음: ${mem_percent}%${NC}"
    echo "  → PHP 메모리 제한 증가 필요"
    echo "  → docker-compose.yml에서 메모리 제한 조정"
else
    echo -e "${GREEN}✓ PHP 메모리 사용률 정상: ${mem_percent}%${NC}"
fi

echo ""
echo "추가 진단 도구:"
echo "1. Query Monitor 설치: ./scripts/install-query-monitor.sh"
echo "2. 최적화 적용: ./scripts/apply-optimizations.sh"
echo "3. 캐시 클리어: docker exec wp-redis redis-cli FLUSHALL"
echo ""
echo "========================================="