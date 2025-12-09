# Slack 알림 통합 설정 가이드

## 개요
이 가이드는 돈린이 수강신청 시스템에 Slack 알림 기능을 설정하는 방법을 안내합니다.
새로운 수강 신청이 접수되면 자동으로 Slack 채널에 알림이 전송됩니다.

## 1. Slack Webhook URL 생성

### 1.1 Slack 앱 페이지 접속
1. https://slack.com/apps/A0F7XDUAZ-incoming-webhooks 접속
2. Slack 워크스페이스에 로그인

### 1.2 Incoming Webhook 앱 추가
1. "Add to Slack" 버튼 클릭
2. 알림을 받을 채널 선택 (예: #수강신청-알림)
3. "Add Incoming WebHooks integration" 클릭

### 1.3 Webhook URL 복사
1. 생성된 Webhook URL 복사
   - 형태: `https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXXXXXXXXXX`
2. 이 URL을 안전한 곳에 보관 (비밀키처럼 중요!)

### 1.4 (선택사항) 커스터마이징
- Customize Name: 알림 봇 이름 변경 가능
- Customize Icon: 알림 봇 아이콘 변경 가능

## 2. WordPress 설정

### 2.1 wp-config-custom.php 파일 편집
```php
// 25번째 줄에 Webhook URL 입력
define('SLACK_WEBHOOK_URL', '여기에_복사한_Webhook_URL_붙여넣기');
```

### 2.2 환경변수로 설정 (권장)
보안을 위해 환경변수 사용을 권장합니다:
```bash
export SLACK_WEBHOOK_URL="https://hooks.slack.com/services/YOUR_URL_HERE"
```

### 2.3 채널 설정 (선택사항)
기본 채널이 아닌 다른 채널로 보내려면:
```php
define('SLACK_CHANNEL', '#다른채널명');
```

### 2.4 알림 비활성화 (필요시)
일시적으로 알림을 끄려면:
```php
define('SLACK_NOTIFICATIONS_ENABLED', false);
```

## 3. 테스트

### 3.1 관리자 페이지에서 테스트
1. WordPress 관리자 로그인
2. "접수자 관리" 메뉴 클릭
3. "Slack 테스트 알림 보내기" 버튼 클릭
4. Slack 채널에서 테스트 메시지 확인

### 3.2 실제 접수 테스트
1. 수강 신청 페이지에서 테스트 신청 제출
2. Slack 채널에서 알림 확인

## 4. 알림 메시지 형식

Slack에 전송되는 알림 메시지 예시:

```
🎉 새로운 수강 신청이 접수되었습니다!

수강 신청 정보
━━━━━━━━━━━━━━━
이름: 홍길동
나이: 30세
전화번호: 010-1234-5678
강의: 돈마고치
접수 시간: 2025-12-09 14:30:00

[관리자 페이지 바로가기]
```

## 5. 문제 해결

### 알림이 오지 않는 경우
1. Webhook URL이 올바르게 설정되었는지 확인
2. `SLACK_NOTIFICATIONS_ENABLED`가 `true`로 설정되었는지 확인
3. WordPress 로그 파일 확인: `wp-content/debug.log`

### 에러 메시지별 해결방법

#### "Slack Webhook URL not configured"
- wp-config-custom.php에 Webhook URL 설정 필요

#### "404 Not Found"
- Webhook URL이 잘못되었거나 삭제됨
- 새로운 Webhook URL 생성 필요

#### "Invalid payload"
- 메시지 형식 문제
- functions.php 파일의 send_slack_notification 함수 확인

## 6. 보안 주의사항

1. **Webhook URL 노출 금지**
   - 절대 공개 저장소에 커밋하지 마세요
   - 환경변수 사용을 권장합니다

2. **정기적인 URL 교체**
   - 보안을 위해 주기적으로 Webhook URL 재생성 권장

3. **권한 관리**
   - wp-config-custom.php 파일 권한을 600으로 설정
   ```bash
   chmod 600 wp-config-custom.php
   ```

## 7. 추가 커스터마이징

### 알림 메시지 수정
functions.php의 `send_slack_notification()` 함수에서 메시지 형식 수정 가능

### 조건부 알림
특정 조건에서만 알림을 보내도록 수정 가능:
```php
// 예: 특정 강의만 알림
if ($course === '돈마고치') {
    send_slack_notification($slack_data);
}
```

## 8. 지원 및 문의

문제가 해결되지 않는 경우:
- WordPress 관리자에게 문의
- 시스템 로그 확인 후 개발팀 연락

---
*최종 업데이트: 2025년 12월 9일*