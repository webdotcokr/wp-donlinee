#!/bin/bash

# WordPress Docker Stack Health Check Script
# This script checks the health of all services in the stack

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
WORDPRESS_URL="http://localhost:8000"
MAX_RETRIES=5
RETRY_DELAY=2

# Function to print colored output
print_status() {
    local status=$1
    local service=$2
    local message=$3

    case $status in
        "success")
            echo -e "${GREEN}✓${NC} $service: $message"
            ;;
        "error")
            echo -e "${RED}✗${NC} $service: $message"
            ;;
        "warning")
            echo -e "${YELLOW}⚠${NC} $service: $message"
            ;;
        *)
            echo "$service: $message"
            ;;
    esac
}

# Function to check container status
check_container() {
    local container_name=$1
    local service_name=$2

    if docker ps --format "table {{.Names}}" | grep -q "^$container_name$"; then
        # Get container health status
        health_status=$(docker inspect --format='{{.State.Health.Status}}' $container_name 2>/dev/null || echo "none")

        if [ "$health_status" = "healthy" ]; then
            print_status "success" "$service_name" "Container is running and healthy"
            return 0
        elif [ "$health_status" = "none" ]; then
            # No health check defined, just check if running
            print_status "success" "$service_name" "Container is running"
            return 0
        else
            print_status "warning" "$service_name" "Container is running but health status is: $health_status"
            return 1
        fi
    else
        print_status "error" "$service_name" "Container is not running"
        return 1
    fi
}

# Function to check service connectivity
check_service() {
    local service=$1
    local check_command=$2
    local service_name=$3

    for i in $(seq 1 $MAX_RETRIES); do
        if eval "$check_command" > /dev/null 2>&1; then
            print_status "success" "$service_name" "Service is responding"
            return 0
        fi

        if [ $i -lt $MAX_RETRIES ]; then
            sleep $RETRY_DELAY
        fi
    done

    print_status "error" "$service_name" "Service is not responding after $MAX_RETRIES attempts"
    return 1
}

# Main health checks
echo "========================================="
echo "WordPress Docker Stack Health Check"
echo "========================================="
echo

# Check Docker daemon
echo "Checking Docker daemon..."
if docker info > /dev/null 2>&1; then
    print_status "success" "Docker" "Docker daemon is running"
else
    print_status "error" "Docker" "Docker daemon is not running"
    exit 1
fi
echo

# Check containers
echo "Checking containers..."
check_container "wp-nginx" "Nginx"
check_container "wp-app" "WordPress"
check_container "wp-redis" "Redis"
check_container "donlinee" "MySQL"
echo

# Check service connectivity
echo "Checking service connectivity..."

# Check Nginx
check_service "nginx" "curl -s -o /dev/null -w '%{http_code}' http://localhost:8000/health | grep -q 200" "Nginx HTTP"

# Check WordPress
check_service "wordpress" "curl -s -o /dev/null -w '%{http_code}' $WORDPRESS_URL | grep -qE '200|301|302'" "WordPress Site"

# Check Redis
check_service "redis" "docker exec wp-redis redis-cli ping | grep -q PONG" "Redis Cache"

# Check MySQL
check_service "mysql" "docker exec donlinee mysqladmin ping --silent" "MySQL Database"
echo

# Check resource usage
echo "Checking resource usage..."
echo
echo "Container Resource Usage:"
docker stats --no-stream --format "table {{.Container}}\t{{.Name}}\t{{.CPUPerc}}\t{{.MemUsage}}\t{{.MemPerc}}"
echo

# Check disk usage
echo "Checking disk usage..."
volumes=$(docker volume ls -q | grep -E "donlinee_.*")
for volume in $volumes; do
    size=$(docker run --rm -v $volume:/data alpine sh -c "du -sh /data" | cut -f1)
    echo "Volume $volume: $size"
done
echo

# Performance metrics
echo "Performance metrics..."

# Check response time
response_time=$(curl -o /dev/null -s -w '%{time_total}' $WORDPRESS_URL)
echo "WordPress response time: ${response_time}s"

# Check cache hit ratio if possible
if docker exec wp-redis redis-cli INFO stats > /dev/null 2>&1; then
    cache_hits=$(docker exec wp-redis redis-cli INFO stats | grep keyspace_hits | cut -d: -f2 | tr -d '\r')
    cache_misses=$(docker exec wp-redis redis-cli INFO stats | grep keyspace_misses | cut -d: -f2 | tr -d '\r')

    if [ -n "$cache_hits" ] && [ -n "$cache_misses" ] && [ $((cache_hits + cache_misses)) -gt 0 ]; then
        hit_ratio=$(echo "scale=2; $cache_hits * 100 / ($cache_hits + $cache_misses)" | bc)
        echo "Redis cache hit ratio: ${hit_ratio}%"
    fi
fi

# Check slow queries
slow_queries=$(docker exec donlinee sh -c "mysql -uroot -p\${MYSQL_ROOT_PASSWORD} -e 'SHOW GLOBAL STATUS LIKE \"Slow_queries\";' 2>/dev/null | grep Slow_queries | awk '{print \$2}'" 2>/dev/null || echo "N/A")
echo "MySQL slow queries: $slow_queries"
echo

# Summary
echo "========================================="
echo "Health Check Summary"
echo "========================================="

all_healthy=true

# Check if all services are running
for container in wp-nginx wp-app wp-redis donlinee; do
    if ! docker ps --format "{{.Names}}" | grep -q "^$container$"; then
        all_healthy=false
        break
    fi
done

if $all_healthy; then
    echo -e "${GREEN}All services are operational!${NC}"
    exit 0
else
    echo -e "${RED}Some services need attention!${NC}"
    exit 1
fi