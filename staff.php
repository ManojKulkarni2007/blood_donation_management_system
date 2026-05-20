<?php
session_start();
if (!isset($_SESSION['donor_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Details - Blood Donation Management System</title>
  <link rel="stylesheet" href="style.css?v=6">
  <style>
    .staff-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 24px;
      max-width: 1100px;
      margin: 0 auto;
    }
    .staff-card {
      background: #fff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 20px rgba(183,28,28,0.1);
      transition: transform 0.25s, box-shadow 0.25s;
      text-align: center;
      border: none;
      position: relative;
    }
    .staff-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 4px;
      background: linear-gradient(90deg, #e53935, #b71c1c);
    }
    .staff-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 12px 30px rgba(183,28,28,0.18);
    }
    .staff-photo-wrap {
      padding: 28px 20px 16px;
    }
    .staff-photo {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #e53935;
      box-shadow: 0 4px 14px rgba(229,57,53,0.25);
    }
    .staff-card h3 {
      font-size: 17px;
      font-weight: 700;
      color: #b71c1c;
      margin: 8px 0 4px;
      padding: 0 16px;
    }
    .staff-role {
      font-size: 12px;
      font-weight: 600;
      background: rgba(229,57,53,0.1);
      color: #e53935;
      display: inline-block;
      padding: 3px 12px;
      border-radius: 20px;
      margin-bottom: 14px;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }
    .staff-info {
      border-top: 1px solid #f5f5f5;
      padding: 14px 20px 20px;
      text-align: left;
    }
    .staff-info p {
      font-size: 13.5px;
      color: #666;
      margin: 7px 0;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .staff-info p strong {
      color: #333;
      min-width: 80px;
    }

    body.dark-theme .staff-card { background: var(--bg-card); border-color: var(--border-color); }
    body.dark-theme .staff-card h3 { color: #ff8a80; }
    body.dark-theme .staff-info { border-top-color: var(--border-color); }
    body.dark-theme .staff-info p { color: #bbb; }
    body.dark-theme .staff-info p strong { color: #eee; }
  </style>
</head>
<body>

  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <div class="page-header">
      <a href="home.php" class="back-link">← Back to Dashboard</a>
      <h1>👥 Staff Details</h1>
      <p>Meet the dedicated medical team behind LifeLine Blood Bank.</p>
    </div>

    <div class="page-body">

      <div class="section-title">🏥 Our Dedicated Staff</div>

      <div class="staff-grid">

        <div class="staff-card">
          <div class="staff-photo-wrap">
            <img src="manoj.jpeg" alt="Dr. Manoj Kulkarni" class="staff-photo">
            <h3>Dr. Manoj Kulkarni</h3>
            <span class="staff-role">Chief Medical Officer</span>
          </div>
          <div class="staff-info">
            <p><strong>📋 Qualification:</strong> MBBS, MD</p>
            <p><strong>💼 Experience:</strong> 10 years</p>
            <p><strong>💰 Salary:</strong> ₹80,000 / month</p>
          </div>
        </div>

        <div class="staff-card">
          <div class="staff-photo-wrap">
            <img src="pavan.jpeg" alt="Dr. Pavan Kulkarni" class="staff-photo">
            <h3>Dr. Pavan Kulkarni</h3>
            <span class="staff-role">Pathologist</span>
          </div>
          <div class="staff-info">
            <p><strong>📋 Qualification:</strong> MBBS, Pathology</p>
            <p><strong>💼 Experience:</strong> 8 years</p>
            <p><strong>💰 Salary:</strong> ₹75,000 / month</p>
          </div>
        </div>

        <div class="staff-card">
          <div class="staff-photo-wrap">
            <img src="laxmans.jpeg" alt="Mr. Laxman S" class="staff-photo" style="object-position: top;">
            <h3>Mr. Laxman S</h3>
            <span class="staff-role">Lab Technician</span>
          </div>
          <div class="staff-info">
            <p><strong>📋 Qualification:</strong> Lab Tech Diploma</p>
            <p><strong>💼 Experience:</strong> 6 years</p>
            <p><strong>💰 Salary:</strong> ₹40,000 / month</p>
          </div>
        </div>

        <div class="staff-card">
          <div class="staff-photo-wrap">
            <img src="akash.jpeg" alt="Mr. Akash G" class="staff-photo">
            <h3>Mr. Akash G</h3>
            <span class="staff-role">Nursing Staff</span>
          </div>
          <div class="staff-info">
            <p><strong>📋 Qualification:</strong> B.Sc Nursing</p>
            <p><strong>💼 Experience:</strong> 5 years</p>
            <p><strong>💰 Salary:</strong> ₹45,000 / month</p>
          </div>
        </div>

      </div>
    </div>

    <footer>
      <p>📞 +91-9876543210 &nbsp;|&nbsp; ✉️ <a href="mailto:support@bdms.com">support@bdms.com</a></p>
      <p>© 2026 Blood Donation Management System. All Rights Reserved.</p>
    </footer>
  </div>

  <script src="theme.js"></script>
</body>
</html>
