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

                <!-- Step 1: 신청 폼 -->
                <div id="enrollment-form-step" class="enrollment-step">
                    <form id="donlinee-enrollment-form" class="donlinee-popup-form">
                        <!-- 안내 사항 -->
                        <div class="donlinee-form-notice">
                            <h3>📋 돈마고치 2기 지원서</h3>
                            <div class="notice-content">
                                <p><strong>[주의 사항]</strong></p>
                                <ol>
                                    <li>100명 이상 접수 시 이후 지원자는 시간 관계상 검토하지 않고 자동 불합격 됩니다.(마감 시 지원서 페이지는 자동 삭제됩니다)</li>
                                    <li>합격 후 지원 취소는 불가능합니다. 간절하게 참여를 원하시는 분만 신청해 주세요.</li>
                                    <li>지원서 중복 접수는 불가능합니다.</li>
                                    <li>해당 설문지는 4월 6일 일요일까지 유효합니다. 이후 지원은 자동 불합격 됩니다.</li>
                                    <li>참가 비용 결제 후 지원 신청이 완료됩니다.</li>
                                </ol>
                            </div>
                            <div class="schedule-info">
                                <p><strong>[강의 안내]</strong></p>
                                <p>시작일: 4월 12일</p>
                                <div class="schedule-details">
                                    <p>1. 강의 수강(1~3주 차)</p>
                                    <p>주말: 토, 일요일 오후 2시~5시 오프라인 현강(서울 강남)</p>
                                    <p>평일: 월~금요일 온라인 수강(장소, 시간 자유)</p>
                                    <p>2. 전체 내용 복습(4주 차)</p>
                                    <p>3. 강제 사업 진행 & 피드백(5~16주 차)</p>
                                </div>
                            </div>
                            <div class="participation-info">
                                <p><strong>[진행 안내]</strong></p>
                                <ol>
                                    <li>모집 일자: 3월 16일 ~ 4월 6일(오후 11시 59분까지)</li>
                                    <li>진행 회차: 서울 강남</li>
                                    <li>참가 비용: 198만원(매출 미발생시 100% 환불)</li>
                                    <li>합격자 발표: 4월 7일 오후 6시(개별 통보)</li>
                                </ol>
                            </div>
                        </div>

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

                        <!-- 환불 계좌 -->
                        <div class="donlinee-form-group">
                            <label for="enrollment-refund">
                                참가 비용 198만원(현금 영수증, 세금계산서 발행 가능) <span class="required">*</span>
                            </label>
                            <p class="form-description">결제 후 지원 신청이 완료됩니다.</p>
                            <p class="form-description" style="color: #d63638; margin-bottom: 10px;">
                                국민은행: 613801-01-651493 (곽경환)
                            </p>
                            <label>참가 비용 환불 계좌 <span class="required">*</span></label>
                            <p class="form-description">불합격 시 4월 14일 일요일 환불됩니다.</p>
                            <p class="form-description">(ex. 홍길동/국민은행/613801-01-651493)</p>
                            <input type="text" id="enrollment-refund" name="refund_account" required
                                   placeholder="예금주명/은행명/계좌번호">
                            <span class="error-message" id="refund-error"></span>
                        </div>

                        <div class="donlinee-form-actions">
                            <button type="submit" class="donlinee-submit-btn">다음 단계 (결제 방법 선택)</button>
                        </div>
                    </form>
                </div>

                <!-- Step 2: 결제 방법 선택 -->
                <div id="payment-method-step" class="enrollment-step" style="display: none;">
                    <div class="payment-success-message">
                        <div class="success-icon">✓</div>
                        <h3>신청이 접수되었습니다!</h3>
                        <p><span id="applicant-name"></span>님의 돈마고치 <?php echo $settings['batch_number']; ?>기 수강 신청이 접수되었습니다.</p>
                        <p>아래 결제 방법 중 하나를 선택해주세요.</p>
                    </div>

                    <div class="payment-methods">
                        <h3>결제 방법 선택</h3>

                        <!-- 계좌이체 -->
                        <div class="payment-method-card" id="bank-transfer-method">
                            <h4>💳 계좌이체</h4>
                            <div class="account-info">
                                <p><strong>입금 계좌 정보</strong></p>
                                <p class="account-number">하나은행 562-910513-14907</p>
                                <p class="account-holder">예금주: 박래완</p>
                                <p class="payment-amount">금액: 1,980,000원</p>
                            </div>
                            <button type="button" class="payment-select-btn" id="select-transfer">
                                계좌이체로 결제하기
                            </button>
                        </div>

                        <!-- 카드결제 -->
                        <div class="payment-method-card" id="card-payment-method">
                            <h4>💳 카드결제</h4>
                            <p>cafe24 결제 페이지로 이동합니다.</p>
                            <p class="payment-notice">신용카드, 체크카드 모두 가능</p>
                            <button type="button" class="payment-select-btn" id="select-card">
                                카드로 결제하기
                            </button>
                        </div>
                    </div>

                    <div class="payment-footer">
                        <button type="button" class="donlinee-back-btn" id="back-to-form">이전 단계로 돌아가기</button>
                    </div>
                </div>

                <!-- Step 3: 결제 안내 완료 -->
                <div id="payment-complete-step" class="enrollment-step" style="display: none;">
                    <div class="payment-complete-message">
                        <div class="success-icon">💰</div>
                        <h3 id="payment-complete-title">계좌이체 안내</h3>
                        <div id="transfer-instructions" class="payment-instructions">
                            <p>아래 계좌로 입금해주시면 신청이 완료됩니다.</p>
                            <div class="account-info-final">
                                <p class="account-number">하나은행 562-910513-14907</p>
                                <p class="account-holder">예금주: 박래완</p>
                                <p class="payment-amount">금액: 1,980,000원</p>
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

        <style>
        /* 폼 스텝 관련 스타일 */
        .enrollment-step {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .donlinee-form-notice {
            background: #f0f0f1;
            border: 1px solid #c3c4c7;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .donlinee-form-notice h3 {
            margin-top: 0;
            color: #2c3338;
        }

        .notice-content ol,
        .schedule-info,
        .participation-info {
            margin: 15px 0;
            line-height: 1.8;
        }

        .schedule-details {
            background: white;
            padding: 10px 15px;
            border-radius: 4px;
            margin: 10px 0;
        }

        .payment-methods {
            display: grid;
            gap: 20px;
            margin: 30px 0;
        }

        .payment-method-card {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .payment-method-card:hover {
            border-color: #2271b1;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .payment-method-card h4 {
            margin-top: 0;
            color: #2c3338;
        }

        .account-info {
            background: #f6f7f7;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }

        .account-number {
            font-size: 18px;
            font-weight: bold;
            color: #2271b1;
            margin: 10px 0;
        }

        .account-holder {
            color: #50575e;
        }

        .payment-amount {
            font-size: 16px;
            font-weight: 600;
            color: #d63638;
            margin-top: 10px;
        }

        .payment-select-btn {
            width: 100%;
            padding: 12px;
            background: #2271b1;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        .payment-select-btn:hover {
            background: #135e96;
        }

        .payment-success-message {
            text-align: center;
            padding: 20px;
            background: #f0f8ff;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .payment-complete-message {
            text-align: center;
            padding: 30px;
        }

        .payment-instructions {
            margin: 20px 0;
        }

        .account-info-final {
            background: #fffbf0;
            border: 2px solid #f0b849;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .donlinee-back-btn {
            background: #50575e;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }

        .donlinee-back-btn:hover {
            background: #3c434a;
        }

        .payment-retry-btn {
            background: #d63638;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        .payment-retry-btn:hover {
            background: #b32d2e;
        }
        </style>
        <?php
    }
}