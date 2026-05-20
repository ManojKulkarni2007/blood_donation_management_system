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
  <title>Request Blood - Blood Donation Management System</title>
  <link rel="stylesheet" href="style.css?v=6">
  <style>
    .form-wrap {
      max-width: 520px;
      margin: 0 auto;
      background: #fff;
      border-radius: 16px;
      padding: 40px;
      box-shadow: 0 8px 30px rgba(183,28,28,0.12);
    }
    .form-wrap h2 {
      text-align: center;
      color: #b71c1c;
      font-size: 26px;
      font-weight: 800;
      margin-bottom: 28px;
    }
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
      margin-bottom: 16px;
    }
    .form-group label {
      display: block;
      font-weight: 600;
      color: #444;
      font-size: 13px;
      margin-bottom: 6px;
    }
    .form-group input,
    .form-group select {
      width: 100%;
      padding: 11px 14px;
      border: 1.5px solid #e0e0e0;
      border-radius: 8px;
      font-size: 14px;
      font-family: inherit;
      background: #fafafa;
      transition: border-color 0.2s, box-shadow 0.2s;
      margin-bottom: 0;
      box-sizing: border-box;
    }
    .form-group input:focus,
    .form-group select:focus {
      outline: none;
      border-color: #e53935;
      box-shadow: 0 0 0 3px rgba(229,57,53,0.1);
      background: #fff;
    }
    .full { margin-bottom: 16px; }
    .submit-btn {
      width: 100%;
      padding: 14px;
      background: linear-gradient(135deg, #e53935, #b71c1c);
      border: none;
      color: #fff;
      font-size: 16px;
      font-weight: 700;
      border-radius: 10px;
      cursor: pointer;
      margin-top: 10px;
      box-shadow: 0 4px 16px rgba(229,57,53,0.35);
      transition: transform 0.2s, box-shadow 0.2s;
      display: block;
    }
    .submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(229,57,53,0.45);
    }
    
    /* Status Modal Styles */
    .status-modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.6);
      backdrop-filter: blur(5px);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 2000;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
    }
    .status-modal-overlay.active { opacity: 1; visibility: visible; }
    .status-modal {
      background: #fff;
      padding: 40px;
      border-radius: 20px;
      width: 90%;
      max-width: 400px;
      position: relative;
      text-align: center;
      transform: translateY(20px) scale(0.95);
      transition: all 0.3s ease;
      box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }
    .status-modal-overlay.active .status-modal { transform: translateY(0) scale(1); }
    body.dark-theme .status-modal { background: var(--bg-card); border: 1px solid var(--border-color); }
    .close-status-modal {
      position: absolute; top: 15px; right: 20px; font-size: 28px; cursor: pointer; color: #888;
    }
    .close-status-modal:hover { color: #e53935; }
    .status-icon { font-size: 64px; margin-bottom: 10px; }
    .status-title { font-size: 24px; color: #333; margin-bottom: 10px; font-weight: 800; }
    body.dark-theme .status-title { color: #fff; }
    .status-msg { font-size: 15px; color: #666; margin-bottom: 25px; line-height: 1.5; }
    body.dark-theme .status-msg { color: #bbb; }
    .status-btn {
      display: inline-block; width: 100%; padding: 12px; background: linear-gradient(135deg, #e53935, #b71c1c); color: #fff; text-decoration: none; font-weight: 700; border-radius: 8px; border: none; cursor: pointer; transition: transform 0.2s;
    }
    .status-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(229,57,53,0.45); }
  </style>
</head>
<body>

  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <div class="page-header">
      <a href="index.php" class="back-link">← Back to Home</a>
      <h1>📋 Request Blood</h1>
      <p>Submit an urgent blood request and we will match you with available donors.</p>
    </div>

    <div class="page-body">
      <div class="form-wrap">
        <h2>Blood Request Form</h2>
        <form action="request.php" method="POST">

          <div class="form-row">
            <div class="form-group">
              <label for="patient_name">Patient Name</label>
              <input type="text" id="patient_name" name="patient_name" placeholder="Full name" required>
            </div>
            <div class="form-group">
              <label for="contact">Contact Number</label>
              <input type="text" id="contact" name="contact" placeholder="+91 XXXXXXXXXX" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="blood_group">Blood Group Required</label>
              <select id="blood_group" name="blood_group" required>
                <option value="">Select</option>
                <option>A+</option><option>A-</option>
                <option>B+</option><option>B-</option>
                <option>O+</option><option>O-</option>
                <option>AB+</option><option>AB-</option>
              </select>
            </div>
            <div class="form-group">
              <label for="units">Units Needed</label>
              <input type="number" id="units" name="units" min="1" placeholder="e.g. 2" required>
            </div>
          </div>

          <div class="full">
            <div class="form-group">
              <label for="hospital">Hospital Name</label>
              <input type="text" id="hospital" name="hospital" placeholder="Hospital / clinic name" required>
            </div>
          </div>

          <button type="submit" class="submit-btn">📋 Submit Request</button>
        </form>
      </div>
    </div>

    <footer>
      <p>📞 +91-9876543210 &nbsp;|&nbsp; ✉️ <a href="mailto:support@bdms.com">support@bdms.com</a></p>
      <p>© 2026 Blood Donation Management System. All Rights Reserved.</p>
    </footer>
  </div>

  <?php if (isset($_GET['status'])): ?>
  <div id="statusModal" class="status-modal-overlay active">
    <div class="status-modal">
      <span class="close-status-modal">&times;</span>
      
      <?php if ($_GET['status'] == 'success'): ?>
        <div class="status-icon">✅</div>
        <h2 class="status-title">Request Submitted!</h2>
        <p class="status-msg">Your blood request has been registered successfully. Our team will process your request shortly. Please stay in touch.</p>
      <?php else: ?>
        <div class="status-icon">❌</div>
        <h2 class="status-title">Submission Failed</h2>
        <p class="status-msg"><?php echo isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : 'An error occurred while submitting your request.'; ?></p>
      <?php endif; ?>
      
      <button class="status-btn close-status-modal-btn">Okay</button>
    </div>
  </div>
  <script>
    const statusModal = document.getElementById('statusModal');
    const closeBtns = document.querySelectorAll('.close-status-modal, .close-status-modal-btn');
    
    closeBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        statusModal.classList.remove('active');
        // Clean URL to prevent showing modal again on page refresh
        window.history.replaceState({}, document.title, window.location.pathname);
      });
    });
    
    window.addEventListener('click', (e) => {
      if (e.target === statusModal) {
        statusModal.classList.remove('active');
        window.history.replaceState({}, document.title, window.location.pathname);
      }
    });
  </script>
  <?php endif; ?>

</body>
</html>
