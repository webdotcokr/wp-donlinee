#!/bin/bash

# Query Monitor 플러그인 설치 스크립트
# 이 플러그인은 WordPress 성능 문제를 디버깅하는 데 필수적입니다

set -e

echo "========================================="
echo "Query Monitor 플러그인 설치"
echo "========================================="

# Docker 컨테이너 확인
if ! docker ps | grep -q wp-app; then
    echo "Error: WordPress 컨테이너가 실행중이 아닙니다."
    echo "docker-compose up -d 명령으로 컨테이너를 시작하세요."
    exit 1
fi

# WP-CLI 설치 확인
echo "WP-CLI 설치 확인..."
docker exec wp-app sh -c "if ! command -v wp &> /dev/null; then
    curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
    chmod +x wp-cli.phar
    mv wp-cli.phar /usr/local/bin/wp
fi"

# Query Monitor 플러그인 설치
echo "Query Monitor 플러그인 설치 중..."
docker exec -u www-data wp-app wp plugin install query-monitor --activate --path=/var/www/html

# 설치 확인
if docker exec -u www-data wp-app wp plugin is-active query-monitor --path=/var/www/html; then
    echo "✅ Query Monitor가 성공적으로 설치되었습니다!"
    echo ""
    echo "사용 방법:"
    echo "1. WordPress 관리자로 로그인"
    echo "2. 상단 관리자 바에서 Query Monitor 메뉴 확인"
    echo "3. 클릭하여 성능 분석 데이터 확인:"
    echo "   - Database Queries: 느린 쿼리 확인"
    echo "   - PHP Errors: PHP 오류 확인"
    echo "   - HTTP API Calls: 외부 API 호출 확인"
    echo "   - Scripts & Styles: 리소스 로딩 확인"
else
    echo "❌ Query Monitor 설치에 실패했습니다."
    echo "수동으로 WordPress 관리자에서 설치해주세요."
fi

echo "========================================="