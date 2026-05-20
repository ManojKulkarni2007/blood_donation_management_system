<?php
include("includes/db.php");
session_start();

$login_success = false;
$login_failed  = false;
$redirect_url  = "index.php";
$display_name  = "";
$error_msg     = "Invalid credentials. Please try again.";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['login_role'] ?? 'donor';
    
    if ($role === 'forgot_password') {
        $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
        $donor_id = mysqli_real_escape_string($conn, $_POST['donor_id'] ?? '');
        $new_password = mysqli_real_escape_string($conn, $_POST['new_password'] ?? '');
        
        // Verify email and donor_id match in Donar table
        $check_sql = "SELECT donar_id FROM Donar WHERE donar_email='$email' AND donar_id='$donor_id'";
        $check_res = mysqli_query($conn, $check_sql);
        if ($check_res && mysqli_num_rows($check_res) > 0) {
            // Update password in donors table
            $update_sql = "UPDATE donors SET password='$new_password' WHERE email='$email' AND role='donor'";
            if (mysqli_query($conn, $update_sql)) {
                $reset_success = true;
            } else {
                $login_failed = true;
                $error_msg = "Error updating password. Please try again.";
            }
        } else {
            $login_failed = true;
            $error_msg = "Invalid Email or Donor ID. Could not reset password.";
        }
    } elseif ($role === 'admin') {
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        
        // Strict admin hardcoded validation (readonly / same credentials)
        if ($email === 'admin' && $password === 'admin') {
            $_SESSION['donor_id']   = 9999; // Unique System Admin ID
            $_SESSION['donor_name'] = 'System Administrator';
            $_SESSION['role']       = 'admin';
            $login_success          = true;
            $display_name          = 'System Administrator';
            $redirect_url           = 'home.php';
        } else {
            $login_failed = true;
            $error_msg    = "Admin authentication failed. Unauthorized access.";
        }
    } else {
        // Donor login logic
        $email    = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
        $password = mysqli_real_escape_string($conn, $_POST['password'] ?? '');
        
        $sql    = "SELECT * FROM donors WHERE email='$email' AND password='$password'";
        $result = mysqli_query($conn, $sql);
        
        if ($result && mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $donor_id = $row['donor_id'];
            
            // Map to correct Donar.donar_id if they are a donor to sync profile and collection history
            if ($row['role'] === 'donor') {
                $donar_sql = "SELECT donar_id FROM Donar WHERE donar_email='$email'";
                $donar_res = mysqli_query($conn, $donar_sql);
                if ($donar_res && mysqli_num_rows($donar_res) > 0) {
                    $donar_row = mysqli_fetch_assoc($donar_res);
                    $donor_id = $donar_row['donar_id'];
                }
            }
            
            $_SESSION['donor_id']   = $donor_id;
            $_SESSION['donor_name'] = $row['name'];
            $_SESSION['role']       = $row['role'];
            $login_success          = true;
            $display_name          = $row['name'];
            $redirect_url           = 'transactions.php'; // Show respective donor history immediately!
        } else {
            $login_failed = true;
            $error_msg    = "Invalid email or password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal Login - LifeLine Blood Bank</title>
  <link rel="stylesheet" href="style.css?v=7">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Outfit', sans-serif;
    }
    
    .login-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 40px 20px;
      flex: 1;
    }

    /* Success / Failure Screen Styling */
    .status-card {
      background: var(--bg-card);
      backdrop-filter: blur(16px);
      border-radius: 24px;
      padding: 50px 40px;
      text-align: center;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
      border: 1px solid var(--border-color);
      max-width: 460px;
      width: 100%;
      animation: zoomIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .status-icon {
      width: 80px; height: 80px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 24px;
      font-size: 38px; color: #fff;
    }
    .status-success { background: linear-gradient(135deg, #43a047, #2e7d32); }
    .status-failed { background: linear-gradient(135deg, #e53935, #b71c1c); }

    .status-card h2 {
      font-size: 26px;
      font-weight: 800;
      margin-bottom: 12px;
    }
    .status-card p {
      font-size: 15.5px;
      color: var(--text-muted);
      margin-bottom: 24px;
    }

    /* Split Role Selector Panel */
    .portal-selector {
      display: flex;
      gap: 30px;
      max-width: 800px;
      width: 100%;
      margin: 40px 0;
      animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .portal-card {
      flex: 1;
      background: var(--bg-card);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid var(--border-color);
      border-radius: 24px;
      padding: 40px 30px;
      text-align: center;
      cursor: pointer;
      transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
      box-shadow: 0 4px 20px rgba(0,0,0,0.05);
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .portal-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 35px rgba(183, 28, 28, 0.15);
      border-color: #ff5252;
    }
    .portal-card.admin-card:hover {
      box-shadow: 0 12px 35px rgba(60, 60, 60, 0.25);
      border-color: #90a4ae;
    }
    .portal-icon {
      width: 80px; height: 80px;
      border-radius: 20px;
      display: flex; align-items: center; justify-content: center;
      font-size: 38px;
      margin-bottom: 24px;
      transition: transform 0.3s;
    }
    .portal-card:hover .portal-icon {
      transform: scale(1.1) rotate(5deg);
    }
    .donor-icon {
      background: rgba(229, 57, 53, 0.1);
      box-shadow: 0 0 20px rgba(229, 57, 53, 0.2);
    }
    .admin-icon {
      background: rgba(96, 125, 139, 0.1);
      box-shadow: 0 0 20px rgba(96, 125, 139, 0.2);
    }
    .portal-card h3 {
      font-size: 22px;
      font-weight: 800;
      color: var(--text-main);
      margin-bottom: 12px;
    }
    .portal-card p {
      font-size: 14.5px;
      color: var(--text-muted);
      line-height: 1.6;
      margin-bottom: 24px;
      flex-grow: 1;
    }
    .portal-btn {
      width: 100%;
      padding: 12px;
      border-radius: 12px;
      font-weight: 700;
      font-size: 14.5px;
      text-transform: uppercase;
      border: none;
      cursor: pointer;
      transition: all 0.3s;
    }
    .donor-btn {
      background: linear-gradient(135deg, #e53935, #b71c1c);
      color: #fff;
      box-shadow: 0 4px 15px rgba(229, 57, 53, 0.3);
    }
    .admin-btn {
      background: linear-gradient(135deg, #78909c, #37474f);
      color: #fff;
      box-shadow: 0 4px 15px rgba(120, 144, 156, 0.3);
    }

    /* Form Panel Styling */
    .form-panel {
      display: none;
      background: var(--bg-card);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid var(--border-color);
      border-radius: 24px;
      padding: 45px;
      width: 100%;
      max-width: 460px;
      box-shadow: 0 10px 35px rgba(0, 0, 0, 0.1);
      animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .form-panel-header {
      text-align: center;
      margin-bottom: 30px;
    }
    .form-panel-header .back-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      color: var(--text-muted);
      text-decoration: none;
      font-size: 14px;
      font-weight: 600;
      margin-bottom: 15px;
      transition: color 0.2s;
    }
    .form-panel-header .back-btn:hover {
      color: #e53935;
    }
    .form-panel-header h2 {
      font-size: 25px;
      font-weight: 800;
      color: var(--text-main);
    }
    .form-panel-header p {
      font-size: 14px;
      color: var(--text-muted);
      margin-top: 6px;
    }

    .form-group {
      margin-bottom: 22px;
      text-align: left;
    }
    .form-group label {
      display: block;
      font-size: 13.5px;
      font-weight: 700;
      color: var(--text-main);
      margin-bottom: 8px;
    }
    .form-group input {
      width: 100%;
      padding: 13px 15px;
      border: 1.5px solid var(--border-color);
      border-radius: 10px;
      font-size: 14.5px;
      font-family: inherit;
      background: rgba(0,0,0,0.02);
      color: var(--text-main);
      transition: all 0.25s;
      box-sizing: border-box;
    }
    body.dark-theme .form-group input {
      background: rgba(255,255,255,0.03);
    }
    .form-group input:focus {
      outline: none;
      border-color: #e53935;
      box-shadow: 0 0 0 3px rgba(229, 57, 53, 0.12);
      background: var(--bg-card);
    }

    /* Non-editable Admin Styling */
    .admin-locked-input {
      background: rgba(0, 0, 0, 0.08) !important;
      border-color: rgba(0, 0, 0, 0.15) !important;
      cursor: not-allowed;
      color: #777 !important;
      font-weight: 600;
    }
    body.dark-theme .admin-locked-input {
      background: rgba(255, 255, 255, 0.06) !important;
      border-color: rgba(255, 255, 255, 0.1) !important;
      color: #999 !important;
    }
    .locked-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(21, 101, 192, 0.08);
      color: #1565c0;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 12.5px;
      font-weight: 600;
      margin-bottom: 20px;
      width: 100%;
      box-sizing: border-box;
    }
    body.dark-theme .locked-badge {
      background: rgba(21, 101, 192, 0.15);
      color: #90caf9;
    }

    .submit-btn {
      width: 100%;
      padding: 14px;
      border-radius: 12px;
      font-size: 15.5px;
      font-weight: 700;
      border: none;
      cursor: pointer;
      transition: all 0.3s;
      margin-top: 10px;
    }
    .donor-submit {
      background: linear-gradient(135deg, #e53935, #b71c1c);
      color: #fff;
      box-shadow: 0 4px 15px rgba(229, 57, 53, 0.3);
    }
    .donor-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 22px rgba(229, 57, 53, 0.4);
    }
    .admin-submit {
      background: linear-gradient(135deg, #455a64, #263238);
      color: #fff;
      box-shadow: 0 4px 15px rgba(69, 90, 100, 0.3);
    }
    .admin-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 22px rgba(69, 90, 100, 0.4);
    }

    .form-panel-footer {
      margin-top: 24px;
      text-align: center;
      font-size: 14px;
      color: var(--text-muted);
    }
    .form-panel-footer a {
      color: #b71c1c;
      font-weight: 700;
      text-decoration: none;
    }
    .form-panel-footer a:hover {
      text-decoration: underline;
    }

    /* Keyframes */
    @keyframes zoomIn {
      from { transform: scale(0.95); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }
    @keyframes fadeInUp {
      from { transform: translateY(20px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
    
    @media (max-width: 768px) {
      .portal-selector {
        flex-direction: column;
        gap: 20px;
      }
      .form-panel {
        padding: 30px;
      }
    }
  </style>
</head>
<body>

  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <div class="login-container">
      
      <?php if ($login_success): ?>
        <!-- Success Screen -->
        <div class="status-card">
          <div class="status-icon status-success">✓</div>
          <h2>Login Successful!</h2>
          <p>Welcome back, <strong><?php echo htmlspecialchars($display_name); ?></strong>!</p>
          <p style="font-size:13px; color:#aaa;">Redirecting to your panel...</p>
        </div>
        <script>
          setTimeout(() => {
            window.location.href = "<?php echo $redirect_url; ?>";
          }, 2000);
        </script>

      <?php elseif (isset($reset_success) && $reset_success): ?>
        <!-- Reset Success Screen -->
        <div class="status-card">
          <div class="status-icon status-success">✓</div>
          <h2>Password Reset Successful!</h2>
          <p>Your password has been updated. You can now login with your new password.</p>
          <a href="login.php" class="submit-btn donor-submit" style="display:inline-block; text-decoration:none; text-align:center;">Go to Login</a>
        </div>

      <?php elseif ($login_failed): ?>
        <!-- Failed Screen -->
        <div class="status-card">
          <div class="status-icon status-failed">✕</div>
          <h2>Login Failed</h2>
          <p><?php echo htmlspecialchars($error_msg); ?></p>
          <a href="login.php" class="submit-btn donor-submit" style="display:inline-block; text-decoration:none; text-align:center;">Try Again</a>
        </div>

      <?php else: ?>
        <!-- Normal Flow: Role Selection & Forms -->
        
        <!-- Header -->
        <div id="login-header" class="page-header" style="text-align:center; max-width:600px; margin-bottom:20px;">
          <h1>👤 Portal Login</h1>
          <p>Please select your login portal to access the Blood Donation Management System.</p>
        </div>

        <!-- 1. Selection Screen -->
        <div id="role-selection" class="portal-selector">
          
          <!-- Donor Card -->
          <div class="portal-card" onclick="showForm('donor')">
            <div class="portal-icon donor-icon">🩸</div>
            <h3>Donor Portal</h3>
            <p>Access your voluntary blood donation history, check medical reports, and track your next eligibility dates.</p>
            <button class="portal-btn donor-btn">Enter Donor Portal</button>
          </div>

          <!-- Admin Card -->
          <div class="portal-card admin-card" onclick="showForm('admin')">
            <div class="portal-icon admin-icon">🛠️</div>
            <h3>Admin Portal</h3>
            <p>Access blood bank stocks, manage registered medical staff, register collections, and view hospital requests.</p>
            <button class="portal-btn admin-btn">Enter Admin Portal</button>
          </div>

        </div>

        <!-- 2. Donor Form Panel -->
        <div id="donor-form" class="form-panel">
          <div class="form-panel-header">
            <a href="javascript:void(0)" class="back-btn" onclick="showSelection()">← Back to Selection</a>
            <h2>Donor Sign In</h2>
            <p>Enter your credentials to view your donation log.</p>
          </div>
          <form action="login.php" method="POST">
            <input type="hidden" name="login_role" value="donor">
            
            <div class="form-group">
              <label for="donor_email">Email Address / User ID</label>
              <input type="email" id="donor_email" name="email" placeholder="you@example.com" required>
            </div>

            <div class="form-group">
              <label for="donor_password">Password</label>
              <div class="password-wrapper" style="position:relative;">
                <input type="password" id="donor_password" name="password" placeholder="Enter your account password" required style="padding-right:40px;">
                <span class="toggle-password" onclick="togglePassword('donor_password')" title="Show/Hide Password" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); cursor:pointer; opacity:0.6; font-size:18px;">👁️</span>
              </div>
            </div>

            <button type="submit" class="submit-btn donor-submit">🔐 Authenticate Donor</button>
          </form>
          <div class="form-panel-footer">
            <a href="javascript:void(0)" onclick="showForgotForm()" style="margin-bottom: 10px; display: inline-block;">Forgot Password?</a><br>
            Don't have a donor account? 
            <a href="donor.php">Register Now →</a>
          </div>
        </div>

        <!-- 3. Admin Form Panel -->
        <div id="admin-form" class="form-panel">
          <div class="form-panel-header">
            <a href="javascript:void(0)" class="back-btn" onclick="showSelection()">← Back to Selection</a>
            <h2>Admin Sign In</h2>
            <p>System Administrator control panel authentication.</p>
          </div>
          
          <div class="locked-badge">
            🔒 Security Lock: Admin credentials are pre-configured & read-only.
          </div>

          <form action="login.php" method="POST">
            <input type="hidden" name="login_role" value="admin">
            
            <div class="form-group">
              <label for="admin_email">Admin Access ID</label>
              <input type="text" id="admin_email" name="email" value="admin" readonly class="admin-locked-input">
            </div>

            <div class="form-group">
              <label for="admin_password">Admin Password</label>
              <div class="password-wrapper" style="position:relative;">
                <input type="password" id="admin_password" name="password" value="admin" readonly class="admin-locked-input" style="padding-right:40px;">
                <span class="toggle-password" onclick="togglePassword('admin_password')" title="Show/Hide Password" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); cursor:pointer; opacity:0.6; font-size:18px;">👁️</span>
              </div>
            </div>

            <button type="submit" class="submit-btn admin-submit">⚙️ Log In as Admin</button>
          </form>
        </div>

        <!-- 4. Forgot Password Form Panel -->
        <div id="forgot-form" class="form-panel">
          <div class="form-panel-header">
            <a href="javascript:void(0)" class="back-btn" onclick="showForm('donor')">← Back to Login</a>
            <h2>Reset Password</h2>
            <p>Enter your details to create a new password.</p>
          </div>
          <form action="login.php" method="POST">
            <input type="hidden" name="login_role" value="forgot_password">
            
            <div class="form-group">
              <label for="forgot_email">Email Address</label>
              <input type="email" id="forgot_email" name="email" placeholder="you@example.com" required>
            </div>
            
            <div class="form-group">
              <label for="forgot_donor_id">Previous Donor ID</label>
              <input type="text" id="forgot_donor_id" name="donor_id" placeholder="Enter your Donor ID (e.g., 1)" required>
            </div>

            <div class="form-group">
              <label for="new_password">New Password</label>
              <div class="password-wrapper" style="position:relative;">
                <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required style="padding-right:40px;">
                <span class="toggle-password" onclick="togglePassword('new_password')" title="Show/Hide Password" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); cursor:pointer; opacity:0.6; font-size:18px;">👁️</span>
              </div>
            </div>

            <button type="submit" class="submit-btn donor-submit">🔄 Reset Password</button>
          </form>
        </div>

      <?php endif; ?>

    </div>

    <footer>
      <p>📞 +91-9876543210 &nbsp;|&nbsp; ✉️ <a href="mailto:support@bdms.com">support@bdms.com</a></p>
      <p>© 2026 Blood Donation Management System. All Rights Reserved.</p>
    </footer>
  </div>

  <script>
    function showForm(role) {
      document.getElementById('role-selection').style.display = 'none';
      document.getElementById('login-header').style.display = 'none';
      document.getElementById('forgot-form').style.display = 'none';
      
      if (role === 'admin') {
        document.getElementById('admin-form').style.display = 'block';
        document.getElementById('donor-form').style.display = 'none';
      } else {
        document.getElementById('donor-form').style.display = 'block';
        document.getElementById('admin-form').style.display = 'none';
      }
    }

    function showForgotForm() {
      document.getElementById('donor-form').style.display = 'none';
      document.getElementById('admin-form').style.display = 'none';
      document.getElementById('role-selection').style.display = 'none';
      document.getElementById('login-header').style.display = 'none';
      document.getElementById('forgot-form').style.display = 'block';
    }

    function showSelection() {
      document.getElementById('admin-form').style.display = 'none';
      document.getElementById('donor-form').style.display = 'none';
      document.getElementById('forgot-form').style.display = 'none';
      document.getElementById('role-selection').style.display = 'flex';
      document.getElementById('login-header').style.display = 'block';
    }

    function togglePassword(inputId) {
      const pwdInput = document.getElementById(inputId);
      const toggleIcon = pwdInput.nextElementSibling;
      
      if (pwdInput.type === 'password') {
        pwdInput.type = 'text';
        toggleIcon.textContent = '🙈';
      } else {
        pwdInput.type = 'password';
        toggleIcon.textContent = '👁️';
      }
    }
  </script>
  <script src="theme.js"></script>
</body>
</html>
