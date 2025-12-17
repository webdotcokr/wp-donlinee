<?php
/**
 * 폼 렌더링 클래스
 */

if (!defined('ABSPATH')) {
    exit;
}

class Donlinee_Enrollment_Forms {

    /**
     * 수강 신청 팝업 렌더링
     */
    public static function render_enrollment_popup() {
        $settings = Donlinee_Enrollment_Settings::get_current_settings();
        ?>
        <!-- 수강 신청 팝업 -->
        <div id="donlinee-enrollment-popup" class="donlinee-popup-overlay" style="display: none;">
            <div class="donlinee-popup-container">
                <div class="donlinee-popup-header">
                    <h2>돈마고치 <?php echo $settings['batch_number']; ?>기 수강 신청</h2>
                    <button type="button" class="donlinee-popup-close">&times;</button>
                </div>

                <!-- 진행 표시바 -->
                <div class="enrollment-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" id="enrollment-progress-fill" style="width: 20%"></div>
                    </div>
                    <div class="progress-steps">
                        <span class="progress-step active" data-step="1">안내사항</span>
                        <span class="progress-step" data-step="2">기본정보</span>
                        <span class="progress-step" data-step="3">지원동기</span>
                        <span class="progress-step" data-step="4">참가비용</span>
                        <span class="progress-step" data-step="5">결제확인</span>
                    </div>
                </div>

                <!-- 폼 컨테이너 -->
                <div id="enrollment-form-step" class="enrollment-step">
                    <form id="donlinee-enrollment-form" class="donlinee-popup-form">

                        <!-- Step 1: 안내사항 -->
                        <div class="form-step step-1 active" data-step="1">
                            <div class="donlinee-form-notice">
                                <div class="notice-content">
                                    <p><strong>[주의 사항]</strong></p>
                                    <ol>
                                        <li>50명 이상 접수 시 이후 지원자는 시간 관계상 검토하지 않고 자동 불합격 됩니다.(마감 시 지원서 페이지는 자동 삭제됩니다)</li>
                                        <li>합격 후 지원 취소는 불가능합니다. 간절하게 참여를 원하시는 분만 신청해 주세요.</li>
                                        <li>지원서 중복 접수는 불가능합니다.</li>
                                        <li>해당 설문지는 12월 28일 일요일까지 유효합니다. 이후 지원은 자동 불합격 됩니다.</li>
                                        <li>참가 비용 결제 후 지원 신청이 완료됩니다.</li>
                                    </ol>
                                </div>
                                <div class="schedule-info">
                                    <p><strong>[강의 안내]</strong></p>
                                    <p>시작일: 2026년 1월 3일 토요일</p>
                                    <div class="schedule-details">
                                        <p>1. 강의 수강(1~4주 차)</p>
                                        <p>주말: 토, 일요일 오후 2시~5시 오프라인 현강(서울 강남)</p>
                                        <p>2. 전체 내용 복습(4주 차)</p>
                                        <p>3. 강제 사업 진행 & 피드백(5~12주 차)</p>
                                    </div>
                                </div>
                                <div class="participation-info">
                                    <p><strong>[진행 안내]</strong></p>
                                    <ol>
                                        <li>모집 일자: 12월 13일 ~ 12월 28일(오후 11시 59분까지)</li>
                                        <li>진행 회차: 서울 강남</li>
                                        <li>참가 비용: 198만원</li>
                                        <li>합격자 발표: 12월 29일 오후 6시(개별 통보)</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="step-buttons">
                                <button type="button" class="btn-next-step" data-next="2">다음</button>
                            </div>
                        </div>

                        <!-- Step 2: 기본정보 -->
                        <div class="form-step step-2" data-step="2">
                            <!-- 성함 -->
                            <div class="donlinee-form-group">
                                <label for="enrollment-name">성함을 입력해 주세요 <span class="required">*</span></label>
                                <input type="text" id="enrollment-name" name="name" required placeholder="홍길동">
                                <span class="error-message" id="name-error"></span>
                            </div>

                            <!-- 나이와 성별 -->
                            <div class="donlinee-form-group">
                                <label for="enrollment-age-gender">나이와 성별을 입력해 주세요 <span class="required">*</span></label>
                                <input type="text" id="enrollment-age-gender" name="age_gender" required placeholder="예: 20대 남성, 30대 여성">
                                <span class="error-message" id="age-gender-error"></span>
                            </div>

                            <!-- 연락처 -->
                            <div class="donlinee-form-group">
                                <label for="enrollment-phone">연락처를 입력해 주세요 <span class="required">*</span></label>
                                <input type="tel" id="enrollment-phone" name="phone" required placeholder="010-0000-0000">
                                <span class="error-message" id="phone-error"></span>
                            </div>

                            <!-- 자기 소개 -->
                            <div class="donlinee-form-group">
                                <label for="enrollment-self-intro">자기 소개 <span class="required">*</span></label>
                                <textarea id="enrollment-self-intro" name="self_intro" rows="4" required
                                          placeholder="간단한 자기소개를 입력해주세요"></textarea>
                                <span class="error-message" id="self-intro-error"></span>
                            </div>

                            <div class="step-buttons">
                                <button type="button" class="btn-prev-step" data-prev="1">이전</button>
                                <button type="button" class="btn-next-step" data-next="3">다음</button>
                            </div>
                        </div>

                        <!-- Step 3: 경험 및 지원동기 -->
                        <div class="form-step step-3" data-step="3">
                            <!-- 상품/서비스 판매 경험 -->
                            <div class="donlinee-form-group">
                                <label for="enrollment-sales-exp">
                                    상품이나 서비스를 팔아본 경험이 있으신가요? (있다면 자세히 작성해 주세요) <span class="required">*</span>
                                </label>
                                <textarea id="enrollment-sales-exp" name="sales_experience" rows="4" required
                                          placeholder="판매 경험이 없으시다면 '없음'이라고 작성해주세요"></textarea>
                                <span class="error-message" id="sales-exp-error"></span>
                            </div>

                            <!-- 지원 이유 -->
                            <div class="donlinee-form-group">
                                <label for="enrollment-reason">지원한 이유 <span class="required">*</span></label>
                                <textarea id="enrollment-reason" name="application_reason" rows="4" required
                                          placeholder="돈마고치에 지원하신 이유를 작성해주세요"></textarea>
                                <span class="error-message" id="reason-error"></span>
                            </div>

                            <!-- 앞으로 하고 싶은 일 (선택) -->
                            <div class="donlinee-form-group">
                                <label for="enrollment-future">앞으로 하고 싶은 일</label>
                                <textarea id="enrollment-future" name="future_plans" rows="4"
                                          placeholder="향후 계획이나 목표가 있다면 작성해주세요 (선택사항)"></textarea>
                            </div>

                            <div class="step-buttons">
                                <button type="button" class="btn-prev-step" data-prev="2">이전</button>
                                <button type="button" class="btn-next-step" data-next="4">다음</button>
                            </div>
                        </div>

                        <!-- Step 4: 참가비용 및 환불계좌 -->
                        <div class="form-step step-4" data-step="4">
                            <!-- 환불 계좌 -->
                            <div class="donlinee-form-group">
                                <label for="enrollment-refund">
                                    참가 비용 198만원(현금 영수증, 세금계산서 발행 가능)
                                </label>
                                <p class="form-description underline mb-4">결제 후 지원 신청이 완료됩니다.</p>
                                <label>참가 비용 환불 계좌 <span class="required">*</span></label>
                                <p class="form-description underline">불합격 시 12월 29일 월요일 환불됩니다.</p>
                                <p class="form-description">(예시, 박래완/하나은행/562-910513-14907)</p>
                                <input type="text" id="enrollment-refund" name="refund_account" required
                                       placeholder="예금주명/은행명/계좌번호">
                                <span class="error-message" id="refund-error"></span>
                            </div>

                            <div class="step-buttons">
                                <button type="button" class="btn-prev-step" data-prev="3">이전</button>
                                <button type="button" class="btn-next-step" data-next="5">다음</button>
                            </div>
                        </div>

                        <!-- Step 5: 최종 확인 및 제출 -->
                        <div class="form-step step-5" data-step="5">
                            <div class="review-section">
                                <h3>입력 정보 확인</h3>
                                <div class="review-content">
                                    <div class="review-item">
                                        <span class="review-label">성함:</span>
                                        <span class="review-value" id="review-name"></span>
                                    </div>
                                    <div class="review-item">
                                        <span class="review-label">나이/성별:</span>
                                        <span class="review-value" id="review-age-gender"></span>
                                    </div>
                                    <div class="review-item">
                                        <span class="review-label">연락처:</span>
                                        <span class="review-value" id="review-phone"></span>
                                    </div>
                                    <div class="review-item">
                                        <span class="review-label">환불계좌:</span>
                                        <span class="review-value" id="review-refund"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- 개인정보 수집·이용 동의 섹션 -->
                            <div class="privacy-agreement-section">
                                <h4>개인정보 수집·이용 동의</h4>
                                <div class="privacy-content-wrapper">
                                    <div class="privacy-scrollable-content">
                                        <div class="privacy-content">
                                            <h5>1. 수집 항목</h5>
                                            <p>• 필수항목: 성명, 나이, 성별, 연락처, 환불계좌정보</p>
                                            <p>• 선택항목: 판매경험, 지원동기, 향후 계획</p>

                                            <h5>2. 수집 및 이용목적</h5>
                                            <p>• 돈마고치 프로그램 신청 및 운영</p>
                                            <p>• 교육비 결제, 환불 등의 거래 처리</p>
                                            <p>• 오프라인 강의 출결 관리 및 과제 제출 확인</p>
                                            <p>• 프로그램 관련 안내 및 고지사항 전달</p>
                                            <p>• 문의 대응 및 불만 처리</p>

                                            <h5>3. 보유 및 이용기간</h5>
                                            <p>• 프로그램 참여자 정보: 프로그램 종료 후 5년</p>
                                            <p>• 교육비 결제 정보: 거래 종료 후 5년 (전자상거래법에 따름)</p>
                                            <p>• 고객 상담 기록: 상담 종료 후 3년</p>

                                            <h5>4. 동의 거부 권리</h5>
                                            <p>귀하는 개인정보 수집·이용에 동의하지 않을 권리가 있습니다. 다만, 필수 항목에 대한 동의를 거부하실 경우 프로그램 신청이 제한될 수 있습니다.</p>

                                            <div class="privacy-full-link">
                                                <a href="/privacy" target="_blank">개인정보처리방침 전문 보기</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="privacy-checkbox-wrapper">
                                    <label class="privacy-checkbox-label">
                                        <input type="checkbox" id="privacy-agreement" name="privacy_agreement" required>
                                        <span>개인정보 수집·이용에 동의합니다 <span class="required">*</span></span>
                                    </label>
                                    <span class="error-message" id="privacy-error"></span>
                                </div>
                            </div>

                            <div class="donlinee-form-actions">
                                <button type="button" class="btn-prev-step" data-prev="4">이전</button>
                                <button type="submit" class="donlinee-submit-btn">신청 완료</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Step 2: 결제 방법 선택 -->
                <div id="payment-method-step" class="enrollment-step" style="display: none;">
                    <div class="payment-success-message">
                        <div class="success-icon">✓</div>
                        <h3>신청서가 접수되었습니다!</h3>
                        <p><span id="applicant-name"></span>님의 돈마고치 <?php echo $settings['batch_number']; ?>기 수강 신청서가 접수되었습니다.</p>
                        <p class="underline text-black text-base">참가 비용 결제 후 지원 신청이 최종 완료됩니다.</p>
                        <!-- <p class="underline">아래 결제 방법 중 하나를 선택해주세요.</p> -->
                    </div>

                    <div class="payment-methods">
                        <h3 class="text-center">결제 방법 선택</h3>

                        <!-- 계좌이체 -->
                        <div class="payment-method-card" id="bank-transfer-method">
                            <h4>계좌이체</h4>
                            <div class="account-info">
                                <p><strong>입금 계좌 정보</strong></p>
                                <p class="account-number">하나은행 562-910513-14907 (박래완)</p>
                                <p class="payment-amount">금액: 1,980,000원</p>
                            </div>
                            <button type="button" class="payment-select-btn" id="select-transfer">
                                빠른 계좌이체 결제하기 →
                            </button>
                        </div>

                        <!-- 카드결제 -->
                        <!-- <div class="payment-method-card" id="card-payment-method">
                            <h4>2. 카드결제</h4>
                            <button type="button" class="payment-select-btn" id="select-card">
                                카드 결제하기 →
                            </button>
                        </div> -->
                    </div>

                    <div class="payment-footer">
                        <button type="button" class="donlinee-back-btn" id="back-to-form">이전 단계로 돌아가기</button>
                    </div>
                </div>

                <!-- Step 3: 결제 안내 완료 -->
                <div id="payment-complete-step" class="enrollment-step" style="display: none;">
                    <div class="payment-complete-message">
                        <h3 id="payment-complete-title">계좌이체 안내</h3>
                        <div id="transfer-instructions" class="payment-instructions">
                            <p>아래 계좌로 입금해주시면 신청이 완료됩니다.</p>
                            <div class="account-info-final">
                                <p class="account-number">하나은행 562-910513-14907</p>
                                <p class="account-holder">예금주: 박래완</p>
                                <p class="payment-amount">금액: 198만원</p>
                            </div>
                            <p class="notice">입금자명을 신청하신 성함과 동일하게 해주세요.</p>
                        </div>
                        <div id="card-instructions" class="payment-instructions" style="display: none;">
                            <p>카드 결제 페이지로 이동 중입니다...</p>
                            <p>새 창이 열리지 않는다면 아래 버튼을 클릭해주세요.</p>
                            <button type="button" class="payment-retry-btn" id="retry-card-payment">
                                결제 페이지 열기
                            </button>
                        </div>
                        <button type="button" class="donlinee-confirm-btn" id="close-enrollment-popup">확인</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}