<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Donor Registration - Blood Donation Management System</title>
  <link rel="stylesheet" href="style.css?v=6">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
  <!-- html2pdf library for beautiful client-side PDF generation -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <style>
    .wizard-container {
      max-width: 800px;
      margin: 0 auto;
      background: #fff;
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 10px 40px rgba(183,28,28,0.08);
      position: relative;
      overflow: hidden;
    }
    body.dark-theme .wizard-container {
      background: var(--bg-card);
      box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    }

    /* Progress Indicators */
    .wizard-progress {
      display: flex;
      justify-content: space-between;
      position: relative;
      margin-bottom: 40px;
    }
    .wizard-progress::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 0;
      right: 0;
      height: 3px;
      background: #f0f0f0;
      z-index: 1;
      transform: translateY(-50%);
    }
    body.dark-theme .wizard-progress::before {
      background: var(--border-color);
    }
    .progress-bar-fill {
      position: absolute;
      top: 50%;
      left: 0;
      width: 0%;
      height: 3px;
      background: linear-gradient(90deg, #e53935, #b71c1c);
      z-index: 1;
      transform: translateY(-50%);
      transition: width 0.4s ease;
    }
    .progress-step {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #f5f5f5;
      color: #999;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 15px;
      z-index: 2;
      position: relative;
      transition: all 0.3s ease;
      border: 3px solid #fff;
    }
    body.dark-theme .progress-step {
      background: var(--bg-main);
      color: #666;
      border-color: var(--bg-card);
    }
    .progress-step.active {
      background: #b71c1c;
      color: #fff;
      box-shadow: 0 0 15px rgba(183,28,28,0.4);
    }
    .progress-step.completed {
      background: #2e7d32;
      color: #fff;
      box-shadow: 0 0 15px rgba(46,125,50,0.3);
    }
    .progress-label {
      position: absolute;
      top: 48px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #888;
      white-space: nowrap;
    }
    .progress-step.active .progress-label {
      color: #b71c1c;
    }
    body.dark-theme .progress-step.active .progress-label {
      color: #ff8a80;
    }

    /* Step Panels */
    .wizard-step-panel {
      display: none;
      animation: fadeIn 0.4s ease forwards;
    }
    .wizard-step-panel.active {
      display: block;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(15px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .section-title {
      font-size: 18px;
      font-weight: 800;
      color: #b71c1c;
      margin-bottom: 24px;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    body.dark-theme .section-title {
      color: #ff8a80;
    }
    .section-title::after {
      content: '';
      flex: 1;
      height: 2px;
      background: linear-gradient(90deg, rgba(183,28,28,0.2), transparent);
    }

    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 24px;
    }
    .form-grid.three-col {
      grid-template-columns: repeat(3, 1fr);
    }
    .form-grid.full-width {
      grid-template-columns: 1fr;
    }
    @media (max-width: 600px) {
      .form-grid, .form-grid.three-col {
        grid-template-columns: 1fr;
      }
    }

    .form-group label {
      display: block;
      font-weight: 600;
      color: #444;
      font-size: 13px;
      margin-bottom: 8px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    body.dark-theme .form-group label {
      color: #bbb;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 12px 16px;
      border: 1.5px solid #e0e0e0;
      border-radius: 10px;
      font-size: 14.5px;
      font-family: inherit;
      background: #fafafa;
      transition: all 0.3s ease;
      box-sizing: border-box;
    }
    body.dark-theme .form-group input,
    body.dark-theme .form-group select,
    body.dark-theme .form-group textarea {
      background: var(--bg-main);
      border-color: var(--border-color);
      color: var(--text-main);
    }
    .form-group input:focus,
    .form-group select:focus {
      outline: none;
      border-color: #e53935;
      box-shadow: 0 0 0 3px rgba(229,57,53,0.15);
      background: #fff;
    }
    .form-group input.invalid,
    .form-group select.invalid {
      border-color: #e53935;
      background: #fff8f8;
    }
    body.dark-theme .form-group input.invalid,
    body.dark-theme .form-group select.invalid {
      background: rgba(229,57,53,0.05);
    }
    .form-group input.valid,
    .form-group select.valid {
      border-color: #2e7d32;
      background: #f8fff8;
    }
    body.dark-theme .form-group input.valid,
    body.dark-theme .form-group select.valid {
      background: rgba(46,125,50,0.05);
    }

    .form-group .hint {
      font-size: 11px;
      color: #888;
      margin-top: 6px;
      font-style: italic;
    }

    .error-feedback {
      color: #d32f2f;
      font-size: 12px;
      margin-top: 6px;
      display: none;
      font-weight: 600;
    }
    .error-feedback.show {
      display: block;
    }

    /* Password inputs */
    .password-wrapper {
      position: relative;
    }
    .password-wrapper input {
      padding-right: 44px;
    }
    .toggle-pwd {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      font-size: 18px;
      opacity: 0.6;
      user-select: none;
    }
    .toggle-pwd:hover {
      opacity: 1;
    }

    /* Buttons */
    .wizard-buttons {
      display: flex;
      justify-content: space-between;
      margin-top: 40px;
      border-top: 1px solid #eee;
      padding-top: 24px;
    }
    body.dark-theme .wizard-buttons {
      border-top-color: var(--border-color);
    }
    .btn-nav {
      padding: 14px 30px;
      border-radius: 12px;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      border: none;
    }
    .btn-next {
      background: linear-gradient(135deg, #e53935, #b71c1c);
      color: #fff;
      box-shadow: 0 4px 15px rgba(229,57,53,0.3);
      margin-left: auto;
    }
    .btn-next:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(229,57,53,0.45);
    }
    .btn-prev {
      background: #f5f5f5;
      color: #666;
    }
    body.dark-theme .btn-prev {
      background: var(--bg-main);
      color: #bbb;
    }
    .btn-prev:hover {
      background: #e0e0e0;
    }
    body.dark-theme .btn-prev:hover {
      background: var(--border-color);
    }
    .btn-nav:disabled {
      opacity: 0.5;
      cursor: not-allowed;
      transform: none !important;
      box-shadow: none !important;
    }

    /* Eligibility Overlay Modal */
    .eligibility-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.65);
      backdrop-filter: blur(8px);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 3000;
      opacity: 0;
      visibility: hidden;
      transition: all 0.4s ease;
    }
    .eligibility-overlay.active {
      opacity: 1;
      visibility: visible;
    }
    .eligibility-modal {
      background: #fff;
      border-radius: 24px;
      padding: 40px;
      max-width: 500px;
      width: 90%;
      text-align: center;
      box-shadow: 0 20px 50px rgba(0,0,0,0.3);
      transform: translateY(30px) scale(0.95);
      transition: all 0.4s ease;
      position: relative;
    }
    body.dark-theme .eligibility-modal {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
    }
    .eligibility-overlay.active .eligibility-modal {
      transform: translateY(0) scale(1);
    }
    .modal-icon {
      font-size: 72px;
      margin-bottom: 20px;
      animation: heartbeat 1.5s infinite;
    }
    @keyframes heartbeat {
      0% { transform: scale(1); }
      20% { transform: scale(1.1); }
      40% { transform: scale(1); }
      60% { transform: scale(1.1); }
      80% { transform: scale(1); }
      100% { transform: scale(1); }
    }
    .modal-title {
      font-size: 26px;
      font-weight: 800;
      color: #2e7d32;
      margin-bottom: 12px;
    }
    .modal-desc {
      font-size: 15px;
      color: #555;
      line-height: 1.6;
      margin-bottom: 28px;
    }
    body.dark-theme .modal-desc {
      color: #ccc;
    }
    .modal-input-group {
      background: #f9f9f9;
      border-radius: 14px;
      padding: 20px;
      margin-bottom: 28px;
      border: 1px solid #f0f0f0;
      text-align: left;
    }
    body.dark-theme .modal-input-group {
      background: var(--bg-main);
      border-color: var(--border-color);
    }
    .modal-input-group label {
      display: block;
      font-weight: 700;
      font-size: 13px;
      color: #333;
      margin-bottom: 8px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    body.dark-theme .modal-input-group label {
      color: #bbb;
    }
    .modal-input-group select {
      width: 100%;
      padding: 12px 14px;
      border: 1.5px solid #ddd;
      border-radius: 8px;
      background: #fff;
      font-size: 15px;
      font-family: inherit;
    }
    body.dark-theme .modal-input-group select {
      background: var(--bg-card);
      border-color: var(--border-color);
      color: var(--text-main);
    }
    .modal-btn {
      width: 100%;
      padding: 15px;
      background: linear-gradient(135deg, #43a047, #2e7d32);
      color: #fff;
      font-weight: 700;
      font-size: 16px;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      box-shadow: 0 4px 15px rgba(46,125,50,0.3);
      transition: all 0.3s ease;
    }
    .modal-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(46,125,50,0.5);
    }

    /* Certificate Preview Card */
    .success-panel {
      text-align: center;
      padding: 30px 10px;
    }
    .success-icon {
      font-size: 64px;
      margin-bottom: 16px;
    }
    .success-panel h2 {
      color: #2e7d32;
      font-size: 26px;
      font-weight: 800;
      margin-bottom: 8px;
    }
    .success-panel p {
      font-size: 15px;
      color: #666;
      margin-bottom: 30px;
    }
    body.dark-theme .success-panel p {
      color: #bbb;
    }
    .success-actions {
      display: flex;
      flex-direction: column;
      gap: 12px;
      max-width: 320px;
      margin: 0 auto;
    }
    .btn-action {
      padding: 14px 20px;
      border-radius: 10px;
      font-weight: 700;
      text-decoration: none;
      display: inline-block;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
      font-size: 15px;
    }
    .btn-action-pdf {
      background: linear-gradient(135deg, #b71c1c, #e53935);
      color: #fff;
      box-shadow: 0 4px 15px rgba(183,28,28,0.25);
    }
    .btn-action-pdf:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(183,28,28,0.4);
    }
    .btn-action-dashboard {
      background: #f5f5f5;
      color: #333;
    }
    body.dark-theme .btn-action-dashboard {
      background: var(--bg-main);
      color: #bbb;
    }
    .btn-action-dashboard:hover {
      background: #e0e0e0;
    }
    body.dark-theme .btn-action-dashboard:hover {
      background: var(--border-color);
    }

    /* PDF template offscreen container */
    #certificate-container {
      position: absolute;
      left: -9999px;
      top: -9999px;
    }
  </style>
</head>
<body>

  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <div class="page-header">
      <a href="home.php" class="back-link">← Back to Dashboard</a>
      <h1>🩸 Donor Registration & Donation</h1>
      <p>Register as a voluntary donor, perform your medical clearance, and generate your donation certificate.</p>
    </div>

    <div class="page-body">
      <div class="wizard-container">

        <!-- Progress Indicator -->
        <div class="wizard-progress">
          <div class="progress-bar-fill" id="progressBarFill"></div>
          <div class="progress-step active" id="stepIndicator1">
            1
            <span class="progress-label">Account Info</span>
          </div>
          <div class="progress-step" id="stepIndicator2">
            2
            <span class="progress-label">Medical Stats</span>
          </div>
          <div class="progress-step" id="stepIndicator3">
            3
            <span class="progress-label">Done</span>
          </div>
        </div>

        <!-- Master Wizard Form -->
        <form id="wizardForm" novalidate>

          <!-- ==============================================
               STEP 1: Account Creation & Age Verification
               ============================================== -->
          <div class="wizard-step-panel active" id="stepPanel1">
            <div class="section-title">👤 Account &amp; Age Verification</div>
            
            <div class="form-grid">
              <div class="form-group">
                <label for="donar_name">Full Username / Name *</label>
                <input type="text" id="donar_name" name="donar_name" placeholder="e.g. Rahul Sharma" required>
                <div class="error-feedback" id="err_name">❌ Username / Name is required.</div>
              </div>
              <div class="form-group">
                <label for="donar_gender">Gender *</label>
                <select id="donar_gender" name="donar_gender" required>
                  <option value="">Select Gender</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                  <option value="Other">Other</option>
                </select>
                <div class="error-feedback" id="err_gender">❌ Gender must be selected.</div>
              </div>
            </div>

            <div class="form-grid">
              <div class="form-group">
                <label for="donar_dob">Date of Birth *</label>
                <input type="date" id="donar_dob" name="donar_dob" required>
                <div class="hint">Age must be 18–65 years to donate</div>
                <div class="error-feedback" id="err_dob">❌ You must be between 18 and 65 years old to donate.</div>
              </div>
              <div class="form-group">
                <label for="donar_age">Calculated Age</label>
                <input type="text" id="donar_age" name="donar_age" placeholder="Auto-calculated" readonly style="background:#f5f5f5; cursor:not-allowed;">
                <div class="hint">Calculated dynamically from Date of Birth</div>
              </div>
            </div>

            <div class="form-grid full-width">
              <div class="form-group">
                <label for="donar_email">User ID / Email Address *</label>
                <input type="email" id="donar_email" name="donar_email" placeholder="you@example.com" required>
                <div class="error-feedback" id="err_email">❌ Enter a valid email address.</div>
              </div>
            </div>

            <div class="form-grid">
              <div class="form-group">
                <label for="reg_password">Create Password *</label>
                <div class="password-wrapper">
                  <input type="password" id="reg_password" name="reg_password" placeholder="Min. 8 characters" required>
                  <span class="toggle-pwd" onclick="togglePassword('reg_password', this)">👁️</span>
                </div>
                <div class="hint">Minimum 8 characters</div>
                <div class="error-feedback" id="err_pwd">❌ Password must be at least 8 characters.</div>
              </div>
              <div class="form-group">
                <label for="reg_confirm">Confirm Password *</label>
                <div class="password-wrapper">
                  <input type="password" id="reg_confirm" name="reg_confirm" placeholder="Re-enter your password" required>
                  <span class="toggle-pwd" onclick="togglePassword('reg_confirm', this)">👁️</span>
                </div>
                <div class="error-feedback" id="err_confirm">❌ Passwords do not match.</div>
              </div>
            </div>
          </div>

          <!-- ==============================================
               STEP 2: Medical Attributes Verification
               ============================================== -->
          <div class="wizard-step-panel" id="stepPanel2">
            <div class="section-title">🩺 Medical Attributes Verification</div>
            
            <div class="form-grid three-col">
              <div class="form-group">
                <label for="donar_weight">Weight (kg) *</label>
                <input type="number" id="donar_weight" name="donar_weight" min="1" step="0.1" placeholder="e.g. 68" required>
                <div class="hint">Minimum 45 kg required</div>
                <div class="error-feedback" id="err_weight">❌ Weight must be at least 45 kg.</div>
              </div>
              <div class="form-group">
                <label for="donar_hemoglobin">Hemoglobin (g/dL) *</label>
                <input type="number" id="donar_hemoglobin" name="donar_hemoglobin" min="1" step="0.1" placeholder="e.g. 13.5" required>
                <div class="hint">Min: 12.5 (F) / 13.0 (M/O)</div>
                <div class="error-feedback" id="err_hb">❌ Hemoglobin below required limits.</div>
              </div>
              <div class="form-group">
                <label for="donar_temperature">Temperature (°F) *</label>
                <input type="number" id="donar_temperature" name="donar_temperature" min="1" step="0.1" placeholder="e.g. 98.6" required>
                <div class="hint">Must be 97.0°F – 99.5°F</div>
                <div class="error-feedback" id="err_temp">❌ Temperature must be 97.0°F – 99.5°F.</div>
              </div>
            </div>

            <div class="form-grid three-col">
              <div class="form-group">
                <label for="donar_bp_systolic">Systolic BP (mmHg) *</label>
                <input type="number" id="donar_bp_systolic" name="donar_bp_systolic" placeholder="e.g. 120" required>
                <div class="hint">Must be 90 – 160 mmHg</div>
                <div class="error-feedback" id="err_systolic">❌ Systolic BP must be 90–160 mmHg.</div>
              </div>
              <div class="form-group">
                <label for="donar_bp_diastolic">Diastolic BP (mmHg) *</label>
                <input type="number" id="donar_bp_diastolic" name="donar_bp_diastolic" placeholder="e.g. 80" required>
                <div class="hint">Must be 60 – 100 mmHg</div>
                <div class="error-feedback" id="err_diastolic">❌ Diastolic BP must be 60–100 mmHg.</div>
              </div>
              <div class="form-group">
                <label for="donar_blood_group">Blood Group *</label>
                <select id="donar_blood_group" name="donar_blood_group" required>
                  <option value="">Select</option>
                  <option>A+</option><option>A-</option>
                  <option>B+</option><option>B-</option>
                  <option>O+</option><option>O-</option>
                  <option>AB+</option><option>AB-</option>
                </select>
                <div class="error-feedback" id="err_blood_group">❌ Blood Group is required.</div>
              </div>
            </div>

            <div class="form-grid">
              <div class="form-group">
                <label for="donar_contact">Contact Number *</label>
                <input type="tel" id="donar_contact" name="donar_contact" placeholder="+91 XXXXXXXXXX" required>
                <div class="error-feedback" id="err_contact">❌ Contact number is required.</div>
              </div>
              <div class="form-group">
                <label for="donar_last_donation">Last Donation Date</label>
                <input type="date" id="donar_last_donation" name="donar_last_donation">
                <div class="hint">Leave blank if first-time donor (Must be 90+ days ago)</div>
                <div class="error-feedback" id="err_lastdon">❌ Last donation must be 90+ days ago.</div>
              </div>
            </div>

            <div class="form-grid full-width">
              <div class="form-group">
                <label for="donar_address">Full Address / City *</label>
                <input type="text" id="donar_address" name="donar_address" placeholder="Street Address, City, State" required>
                <div class="error-feedback" id="err_address">❌ Address is required.</div>
              </div>
            </div>

            <div class="form-grid full-width">
              <div class="form-group">
                <label for="donar_medical_conditions">Medical Conditions / Medications (Optional)</label>
                <input type="text" id="donar_medical_conditions" name="donar_medical_conditions" placeholder="e.g. None / On aspirin / Diabetes">
              </div>
            </div>
          </div>

          <!-- ==============================================
               STEP 3: Complete Success State
               ============================================== -->
          <div class="wizard-step-panel" id="stepPanel3">
            <div class="success-panel">
              <div class="success-icon">🎉</div>
              <h2>Donation Complete!</h2>
              <p>Thank you for your noble contribution. Your account has been registered, stock has been updated, and your transaction logged.</p>
              
              <!-- VISIBLE INTERACTIVE CERTIFICATE PREVIEW -->
              <div style="margin: 25px auto; overflow-x: auto; max-width: 100%; display: flex; justify-content: center; padding: 10px 0;">
                <div id="pdf-certificate" style="width: 700px; min-width: 700px; padding: 40px; font-family: 'Outfit', 'Inter', sans-serif; border: 8px double #b71c1c; background: #ffffff; color: #111111; box-sizing: border-box; position: relative; text-align: left; box-shadow: 0 8px 30px rgba(0,0,0,0.12); border-radius: 4px;">
                  <!-- Watermark logo -->
                  <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 220px; opacity: 0.03; color: #b71c1c; pointer-events: none; z-index: 1;">🩸</div>
                  
                  <div style="position: relative; z-index: 2;">
                    <!-- Header -->
                    <div style="text-align: center; margin-bottom: 20px;">
                      <h1 style="color: #b71c1c; font-size: 28px; margin: 0; text-transform: uppercase; font-weight: 800; letter-spacing: 1px;">LifeLine Blood Bank</h1>
                      <p style="margin: 5px 0 2px; font-size: 13px; color: #555; font-weight: 600;">Address: 123 Main Road, Hubli, Karnataka</p>
                      <p style="margin: 2px 0; font-size: 13px; color: #555; font-weight: 600;">Phone: +91-9876543210 &nbsp;|&nbsp; Email: support@bdms.com</p>
                    </div>

                    <hr style="border: 0; border-top: 2px solid #b71c1c; margin: 0 0 25px 0;">

                    <!-- Main Title -->
                    <div style="text-align: center; margin-bottom: 25px;">
                      <h2 style="font-size: 20px; color: #2e7d32; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; margin: 0 0 10px 0;">Certificate of Blood Donation</h2>
                      <p style="font-size: 13px; color: #666; font-style: italic; margin: 0;">Awarded for voluntary and noble public service</p>
                    </div>

                    <!-- Certificate Content -->
                    <div style="text-align: center; margin-bottom: 35px; padding: 0 10px;">
                      <p style="font-size: 15px; line-height: 1.8; color: #333; margin: 0;">
                        This certificate is proudly presented to <strong style="color: #b71c1c; font-size: 18px; font-weight: 800; border-bottom: 1.5px dashed #b71c1c; padding-bottom: 2px;" id="pdf_donor_name">Rahul Sharma</strong>,
                        aged <strong style="color: #000; font-weight: 700;" id="pdf_donor_age">28</strong>, in deep appreciation of their generous voluntary contribution of 
                        <strong style="color: #b71c1c; font-size: 18px; font-weight: 800;" id="pdf_donor_amount">350 ml</strong> of <strong style="color: #000; font-weight: 700;" id="pdf_donor_group">O+</strong> blood.
                      </p>
                      <p style="font-size: 13px; color: #555; line-height: 1.7; margin: 20px 0 0 0;">
                        This lifesaving donation took place on <strong style="color: #000; font-weight: 700;" id="pdf_donation_date">2026-05-19</strong>. Your contribution plays a critical role in providing medical treatment and emergency care to patients in need. We salute your humanitarian effort.
                      </p>
                    </div>

                    <!-- Footer signatures -->
                    <div style="margin-top: 40px; display: flex; justify-content: space-between; align-items: flex-end;">
                      <div style="font-size: 11px; color: #555; text-align: left; line-height: 1.5;">
                        <p style="margin: 0;"><strong>Certificate ID:</strong> BDMS-TXN-<span id="pdf_cert_id">10293</span></p>
                        <p style="margin: 2px 0 0 0;"><strong>Verification Code:</strong> BDMS-APPROVED-<span id="pdf_cert_code">A8C9</span></p>
                        <p style="margin: 2px 0 0 0; color: #2e7d32; font-weight: 700;">✓ Fully Cleared &amp; Approved</p>
                      </div>
                      <div style="text-align: right; width: 220px;">
                        <div style="font-family: 'Dancing Script', cursive; font-size: 24px; color: #122240; margin-bottom: 2px;">Dr. Manoj Kulkarni</div>
                        <hr style="border: 0; border-top: 1px solid #888; margin: 4px 0;">
                        <p style="margin: 0; font-size: 11px; font-weight: 700; color: #111; text-transform: uppercase;">Dr. Manoj Kulkarni, MD</p>
                        <p style="margin: 2px 0 0 0; font-size: 10px; color: #666; font-weight: 600;">Medical Director, LifeLine Blood Bank</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="success-actions">
                <button type="button" class="btn-action btn-action-pdf" id="btnDownloadPDF">📥 Download Certificate (PDF)</button>
                <a href="home.php" class="btn-action btn-action-dashboard">📊 Go to Dashboard</a>
              </div>
            </div>
          </div>

          <!-- Navigation buttons -->
          <div class="wizard-buttons" id="wizardButtonsRow">
            <button type="button" class="btn-nav btn-prev" id="btnPrev" style="display:none;">Back</button>
            <button type="button" class="btn-nav btn-next" id="btnNext">Next Step</button>
          </div>

        </form>
      </div>
    </div>

    <footer>
      <p>📞 +91-9876543210 &nbsp;|&nbsp; ✉️ <a href="mailto:support@bdms.com">support@bdms.com</a></p>
      <p>© 2026 Blood Donation Management System. All Rights Reserved.</p>
    </footer>
  </div>

  <!-- ==============================================
       ELIGIBILITY SUCCESS MODAL (STEP 2 POP-UP)
       ============================================== -->
  <div class="eligibility-overlay" id="eligibilityOverlay">
    <div class="eligibility-modal">
      <div class="modal-icon">❤️</div>
      <h2 class="modal-title">Eligibility Confirmed!</h2>
      <p class="modal-desc">
        Congratulations! You have satisfied all vital signs and medical parameters checked for blood donation. 
        You are completely fit and cleared to donate blood.
      </p>

      <div class="modal-input-group">
        <label for="donated_amount">Amount of Blood Donated *</label>
        <select id="donated_amount">
          <option value="350">350 ml (Standard Single Unit)</option>
          <option value="450">450 ml (Double / Large Unit)</option>
        </select>
      </div>

      <button type="button" class="modal-btn" id="btnConfirmDonation">Submit Donation &amp; Download Report</button>
    </div>
  </div>



  <script src="theme.js"></script>
  <script>
    let currentStep = 1;
    let registeredData = null; // Stored response from AJAX

    // UI elements
    const stepPanel1 = document.getElementById('stepPanel1');
    const stepPanel2 = document.getElementById('stepPanel2');
    const stepPanel3 = document.getElementById('stepPanel3');

    const stepIndicator1 = document.getElementById('stepIndicator1');
    const stepIndicator2 = document.getElementById('stepIndicator2');
    const stepIndicator3 = document.getElementById('stepIndicator3');

    const progressBarFill = document.getElementById('progressBarFill');

    const btnPrev = document.getElementById('btnPrev');
    const btnNext = document.getElementById('btnNext');
    const wizardButtonsRow = document.getElementById('wizardButtonsRow');

    const eligibilityOverlay = document.getElementById('eligibilityOverlay');
    const btnConfirmDonation = document.getElementById('btnConfirmDonation');
    const btnDownloadPDF = document.getElementById('btnDownloadPDF');

    // Toggle Password visibility
    function togglePassword(inputId, icon) {
      const inp = document.getElementById(inputId);
      if (inp.type === 'password') {
        inp.type = 'text';
        icon.textContent = '🙈';
      } else {
        inp.type = 'password';
        icon.textContent = '👁️';
      }
    }

    // Dynamic age calculation from date of birth
    document.getElementById('donar_dob').addEventListener('change', function() {
      const dobVal = this.value;
      if (!dobVal) return;
      const dob = new Date(dobVal);
      const today = new Date();
      let age = today.getFullYear() - dob.getFullYear();
      const m = today.getMonth() - dob.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
        age--;
      }
      document.getElementById('donar_age').value = age;
      
      const isAgeValid = age >= 18 && age <= 65;
      this.classList.toggle('invalid', !isAgeValid);
      this.classList.toggle('valid', isAgeValid);
      toggleErr('err_dob', !isAgeValid);
    });

    function toggleErr(id, show) {
      const err = document.getElementById(id);
      if (err) err.classList.toggle('show', show);
    }

    // Form Navigation & Checks
    btnNext.addEventListener('click', () => {
      if (currentStep === 1) {
        if (validateStep1()) {
          goToStep(2);
        }
      } else if (currentStep === 2) {
        if (validateStep2()) {
          // Open the Success Eligibility Modal popup
          eligibilityOverlay.classList.add('active');
        }
      }
    });

    btnPrev.addEventListener('click', () => {
      if (currentStep > 1) {
        goToStep(currentStep - 1);
      }
    });

    function goToStep(step) {
      currentStep = step;

      // Update Panel visibility
      stepPanel1.classList.toggle('active', step === 1);
      stepPanel2.classList.toggle('active', step === 2);
      stepPanel3.classList.toggle('active', step === 3);

      // Update Indicators
      stepIndicator1.classList.toggle('active', step === 1);
      stepIndicator1.classList.toggle('completed', step > 1);

      stepIndicator2.classList.toggle('active', step === 2);
      stepIndicator2.classList.toggle('completed', step > 2);

      stepIndicator3.classList.toggle('active', step === 3);
      stepIndicator3.classList.toggle('completed', step > 3);

      // Progress bar fill percentage
      if (step === 1) progressBarFill.style.width = '0%';
      if (step === 2) progressBarFill.style.width = '50%';
      if (step === 3) progressBarFill.style.width = '100%';

      // Button rows
      btnPrev.style.display = step === 1 || step === 3 ? 'none' : 'block';
      
      if (step === 3) {
        wizardButtonsRow.style.display = 'none';
      } else {
        wizardButtonsRow.style.display = 'flex';
        btnNext.textContent = step === 2 ? 'Clear & Donate Blood 🩸' : 'Next Step';
      }
    }

    // Step 1 Validation (Account & Age check)
    function validateStep1() {
      let isValid = true;
      const name = document.getElementById('donar_name');
      const gender = document.getElementById('donar_gender');
      const dob = document.getElementById('donar_dob');
      const ageInput = document.getElementById('donar_age');
      const email = document.getElementById('donar_email');
      const pwd = document.getElementById('reg_password');
      const confirm = document.getElementById('reg_confirm');

      // Username
      if (!name.value.trim()) {
        name.classList.add('invalid');
        toggleErr('err_name', true);
        isValid = false;
      } else {
        name.classList.remove('invalid');
        name.classList.add('valid');
        toggleErr('err_name', false);
      }

      // Gender
      if (!gender.value) {
        gender.classList.add('invalid');
        toggleErr('err_gender', true);
        isValid = false;
      } else {
        gender.classList.remove('invalid');
        gender.classList.add('valid');
        toggleErr('err_gender', false);
      }

      // DOB & Age Check
      const ageVal = parseInt(ageInput.value);
      if (!dob.value || isNaN(ageVal) || ageVal < 18 || ageVal > 65) {
        dob.classList.add('invalid');
        toggleErr('err_dob', true);
        isValid = false;
      } else {
        dob.classList.remove('invalid');
        dob.classList.add('valid');
        toggleErr('err_dob', false);
      }

      // Email
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!email.value.trim() || !emailRegex.test(email.value)) {
        email.classList.add('invalid');
        toggleErr('err_email', true);
        isValid = false;
      } else {
        email.classList.remove('invalid');
        email.classList.add('valid');
        toggleErr('err_email', false);
      }

      // Password
      if (pwd.value.length < 8) {
        pwd.classList.add('invalid');
        toggleErr('err_pwd', true);
        isValid = false;
      } else {
        pwd.classList.remove('invalid');
        pwd.classList.add('valid');
        toggleErr('err_pwd', false);
      }

      // Confirm Password
      if (confirm.value !== pwd.value || !confirm.value) {
        confirm.classList.add('invalid');
        toggleErr('err_confirm', true);
        isValid = false;
      } else {
        confirm.classList.remove('invalid');
        confirm.classList.add('valid');
        toggleErr('err_confirm', false);
      }

      return isValid;
    }

    // Step 2 Validation (Medical Parameters Check)
    function validateStep2() {
      let isValid = true;
      
      const weight = document.getElementById('donar_weight');
      const hb = document.getElementById('donar_hemoglobin');
      const temp = document.getElementById('donar_temperature');
      const bpSys = document.getElementById('donar_bp_systolic');
      const bpDia = document.getElementById('donar_bp_diastolic');
      const bloodGroup = document.getElementById('donar_blood_group');
      const contact = document.getElementById('donar_contact');
      const address = document.getElementById('donar_address');
      const lastDon = document.getElementById('donar_last_donation');
      const gender = document.getElementById('donar_gender').value;

      // Weight: >= 45
      const weightVal = parseFloat(weight.value);
      if (isNaN(weightVal) || weightVal < 45) {
        weight.classList.add('invalid');
        toggleErr('err_weight', true);
        isValid = false;
      } else {
        weight.classList.remove('invalid');
        weight.classList.add('valid');
        toggleErr('err_weight', false);
      }

      // Hb check: gender-specific: Min 12.5 (F) / 13.0 (M)
      const hbVal = parseFloat(hb.value);
      const minHb = (gender === 'Female') ? 12.5 : 13.0;
      if (isNaN(hbVal) || hbVal < minHb) {
        hb.classList.add('invalid');
        document.getElementById('err_hb').textContent = `❌ Hemoglobin is below limits (Min ${minHb} g/dL required for ${gender}).`;
        toggleErr('err_hb', true);
        isValid = false;
      } else {
        hb.classList.remove('invalid');
        hb.classList.add('valid');
        toggleErr('err_hb', false);
      }

      // Temperature: 97.0 - 99.5
      const tempVal = parseFloat(temp.value);
      if (isNaN(tempVal) || tempVal < 97.0 || tempVal > 99.5) {
        temp.classList.add('invalid');
        toggleErr('err_temp', true);
        isValid = false;
      } else {
        temp.classList.remove('invalid');
        temp.classList.add('valid');
        toggleErr('err_temp', false);
      }

      // BP Systolic: 90 - 160
      const sysVal = parseInt(bpSys.value);
      if (isNaN(sysVal) || sysVal < 90 || sysVal > 160) {
        bpSys.classList.add('invalid');
        toggleErr('err_systolic', true);
        isValid = false;
      } else {
        bpSys.classList.remove('invalid');
        bpSys.classList.add('valid');
        toggleErr('err_systolic', false);
      }

      // BP Diastolic: 60 - 100
      const diaVal = parseInt(bpDia.value);
      if (isNaN(diaVal) || diaVal < 60 || diaVal > 100) {
        bpDia.classList.add('invalid');
        toggleErr('err_diastolic', true);
        isValid = false;
      } else {
        bpDia.classList.remove('invalid');
        bpDia.classList.add('valid');
        toggleErr('err_diastolic', false);
      }

      // Blood Group
      if (!bloodGroup.value) {
        bloodGroup.classList.add('invalid');
        toggleErr('err_blood_group', true);
        isValid = false;
      } else {
        bloodGroup.classList.remove('invalid');
        bloodGroup.classList.add('valid');
        toggleErr('err_blood_group', false);
      }

      // Contact No
      if (!contact.value.trim()) {
        contact.classList.add('invalid');
        toggleErr('err_contact', true);
        isValid = false;
      } else {
        contact.classList.remove('invalid');
        contact.classList.add('valid');
        toggleErr('err_contact', false);
      }

      // Address
      if (!address.value.trim()) {
        address.classList.add('invalid');
        toggleErr('err_address', true);
        isValid = false;
      } else {
        address.classList.remove('invalid');
        address.classList.add('valid');
        toggleErr('err_address', false);
      }

      // Last donation check: >= 90 days ago
      if (lastDon.value) {
        const lastDonDate = new Date(lastDon.value);
        const today = new Date();
        const daysDiff = Math.floor((today - lastDonDate) / (1000 * 60 * 60 * 24));
        if (daysDiff < 90) {
          lastDon.classList.add('invalid');
          toggleErr('err_lastdon', true);
          isValid = false;
        } else {
          lastDon.classList.remove('invalid');
          lastDon.classList.add('valid');
          toggleErr('err_lastdon', false);
        }
      } else {
        lastDon.classList.remove('invalid');
        toggleErr('err_lastdon', false);
      }

      return isValid;
    }

    // Step 3 Submission via AJAX & Trigger PDF
    btnConfirmDonation.addEventListener('click', () => {
      // Close overlay
      eligibilityOverlay.classList.remove('active');

      const donatedAmount = document.getElementById('donated_amount').value;

      // Build parameters
      const formData = new URLSearchParams();
      formData.append('ajax', '1');
      formData.append('donated_amount', donatedAmount);
      formData.append('donar_name', document.getElementById('donar_name').value);
      formData.append('donar_gender', document.getElementById('donar_gender').value);
      formData.append('donar_age', document.getElementById('donar_age').value);
      formData.append('donar_dob', document.getElementById('donar_dob').value);
      formData.append('donar_email', document.getElementById('donar_email').value);
      formData.append('reg_password', document.getElementById('reg_password').value);
      formData.append('reg_confirm', document.getElementById('reg_confirm').value);
      
      formData.append('donar_weight', document.getElementById('donar_weight').value);
      formData.append('donar_hemoglobin', document.getElementById('donar_hemoglobin').value);
      formData.append('donar_temperature', document.getElementById('donar_temperature').value);
      formData.append('donar_bp_systolic', document.getElementById('donar_bp_systolic').value);
      formData.append('donar_bp_diastolic', document.getElementById('donar_bp_diastolic').value);
      formData.append('donar_blood_group', document.getElementById('donar_blood_group').value);
      formData.append('donar_contact', document.getElementById('donar_contact').value);
      formData.append('donar_address', document.getElementById('donar_address').value);
      formData.append('donar_last_donation', document.getElementById('donar_last_donation').value);
      formData.append('donar_medical_conditions', document.getElementById('donar_medical_conditions').value);

      // Perform Fetch Post request
      fetch('donors.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData.toString()
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          registeredData = data;
          
          // Populate the Print Template Div values dynamically
          document.getElementById('pdf_donor_name').textContent = data.name;
          document.getElementById('pdf_donor_age').textContent = data.age;
          document.getElementById('pdf_donor_group').textContent = data.blood_group;
          document.getElementById('pdf_donor_amount').textContent = data.amount + ' ml';
          document.getElementById('pdf_donation_date').textContent = data.date;
          document.getElementById('pdf_cert_id').textContent = data.donor_id + '-' + Math.floor(Math.random() * 90000 + 10000);
          document.getElementById('pdf_cert_code').textContent = 'SECURE-' + Math.floor(Math.random() * 900 + 100);

          // Transition to Step 3 success screen
          goToStep(3);

          // Download PDF Certificate automatically after layout renders fully
          setTimeout(() => {
            downloadCertificatePDF();
          }, 500);
        } else {
          // Show database error
          alert('Error: ' + data.msg.replace(/\|/g, '\n'));
        }
      })
      .catch(err => {
        console.error(err);
        alert('Server connection error. Please try again.');
      });
    });

    // Generate and Download Certificate PDF Function
    function downloadCertificatePDF() {
      if (!registeredData) return;
      const element = document.getElementById('pdf-certificate');
      
      const opt = {
        margin:       10,
        filename:     `blood_donation_certificate_${registeredData.name.replace(/\s+/g, '_')}.pdf`,
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2.5, useCORS: true },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
      };

      // Generate PDF
      html2pdf().from(element).set(opt).save();
    }

    btnDownloadPDF.addEventListener('click', downloadCertificatePDF);
  </script>
</body>
</html>
