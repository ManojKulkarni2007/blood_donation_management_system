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
  <title>Blood Donation Management System</title>
  <link rel="stylesheet" href="style.css?v=6">
  <script>
    // Immediate execution in head block to prevent layout theme flashing
    if (localStorage.getItem('theme') === 'dark') {
        document.documentElement.classList.add('dark-theme');
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    /* ── Reset & Base ── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', Arial, sans-serif;
      background: #f8f0f0;
      color: #2c2c2c;
      display: flex;
      min-height: 100vh;
    }

    /* ══════════════════════════════
       LEFT SIDEBAR
    ══════════════════════════════ */
    .sidebar {
      width: 240px;
      min-height: 100vh;
      background: linear-gradient(180deg, #7b0000 0%, #b71c1c 40%, #c62828 100%);
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 30px 0 20px;
      position: fixed;
      left: 0; top: 0; bottom: 0;
      z-index: 100;
      box-shadow: 4px 0 20px rgba(0,0,0,0.25);
    }

    .sidebar-logo {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
      padding: 0 20px 28px;
      border-bottom: 1px solid rgba(255,255,255,0.15);
      width: 100%;
      text-align: center;
    }
    .sidebar-logo img {
      width: 64px;
      height: 64px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid rgba(255,255,255,0.4);
      box-shadow: 0 4px 14px rgba(0,0,0,0.3);
    }
    .sidebar-logo span {
      color: #fff;
      font-size: 13px;
      font-weight: 700;
      letter-spacing: 0.5px;
      line-height: 1.4;
    }

    .sidebar nav {
      width: 100%;
      margin-top: 20px;
      flex: 1;
    }
    .sidebar nav ul {
      list-style: none;
      padding: 0 12px;
    }
    .sidebar nav ul li {
      margin-bottom: 4px;
    }
    .sidebar nav ul li a {
      display: flex;
      align-items: center;
      gap: 12px;
      color: rgba(255,255,255,0.82);
      text-decoration: none;
      font-size: 14.5px;
      font-weight: 500;
      padding: 11px 16px;
      border-radius: 10px;
      transition: all 0.25s;
    }
    .sidebar nav ul li a:hover,
    .sidebar nav ul li a.active {
      background: rgba(255,255,255,0.18);
      color: #fff;
      padding-left: 22px;
      box-shadow: inset 3px 0 0 #fff;
    }
    .sidebar nav ul li a .nav-icon {
      font-size: 17px;
      width: 22px;
      text-align: center;
    }

    .sidebar-footer {
      padding: 18px 20px;
      border-top: 1px solid rgba(255,255,255,0.15);
      width: 100%;
      text-align: center;
      color: rgba(255,255,255,0.5);
      font-size: 11px;
    }

    /* ══════════════════════════════
       MAIN CONTENT
    ══════════════════════════════ */
    .main-content {
      margin-left: 240px;
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    /* ── HERO BANNER ── */
    .hero {
      position: relative;
      width: 100%;
      height: 420px;
      overflow: hidden;
    }
    .hero img.hero-bg {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center 30%;
      display: block;
      position: absolute;
      top: 0; left: 0;
      z-index: 0;
    }
    .hero-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(0deg, rgba(0,0,0,0.85) 0%, transparent 60%);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-end;
      padding: 0 40px 25px;
      z-index: 1;
    }
    .hero p {
      font-size: 19px;
      color: rgba(255,255,255,0.95);
      margin-bottom: 0;
      max-width: 800px;
      text-align: center;
      line-height: 1.6;
      position: relative;
      z-index: 2;
      text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    }
    .hero-actions-standalone {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-bottom: 40px;
    }
    .btn-primary {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: #fff;
      color: #b71c1c;
      padding: 13px 28px;
      border-radius: 10px;
      font-size: 15px;
      font-weight: 700;
      text-decoration: none;
      border: none;
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
      box-shadow: 0 4px 18px rgba(0,0,0,0.2);
    }
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(0,0,0,0.3);
    }
    .btn-outline {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: transparent;
      color: #fff;
      padding: 13px 28px;
      border-radius: 10px;
      font-size: 15px;
      font-weight: 700;
      text-decoration: none;
      border: 2px solid rgba(255,255,255,0.6);
      cursor: pointer;
      transition: all 0.2s;
    }
    .btn-outline:hover {
      background: rgba(255,255,255,0.15);
      border-color: #fff;
      transform: translateY(-2px);
    }

    /* ── PAGE BODY ── */
    .page-body {
      padding: 40px 50px;
      background: #f8f0f0;
      flex: 1;
    }

    /* ── STATS SECTION ── */
    .section-title {
      font-size: 22px;
      font-weight: 700;
      color: #b71c1c;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .section-title::after {
      content: '';
      flex: 1;
      height: 2px;
      background: linear-gradient(to right, #e5393540, transparent);
      border-radius: 2px;
    }

    .stats {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 18px;
      margin-bottom: 48px;
    }
    .stats .card {
      background: #fff;
      border-radius: 14px;
      padding: 24px 20px;
      text-align: center;
      box-shadow: 0 4px 16px rgba(183,28,28,0.09);
      border: none;
      transition: transform 0.25s, box-shadow 0.25s;
      position: relative;
      overflow: hidden;
      width: auto;
      margin: 0;
    }
    .stats .card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 4px;
      background: linear-gradient(90deg, #e53935, #b71c1c);
    }
    .stats .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 10px 28px rgba(183,28,28,0.16);
      background: #fff;
    }
    .stats .card .stat-icon {
      font-size: 32px;
      margin-bottom: 10px;
    }
    .stats .card .stat-label {
      font-size: 12.5px;
      color: #888;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      margin-bottom: 6px;
    }
    .stats .card strong {
      font-size: 34px;
      font-weight: 800;
      color: #b71c1c;
      display: block;
    }

    /* ── BLOOD AVAILABILITY ── */
    .availability {
      margin-bottom: 48px;
    }
    .blood-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
      gap: 14px;
    }
    .blood-card {
      background: linear-gradient(135deg, #e53935, #b71c1c);
      color: #fff;
      border-radius: 12px;
      padding: 18px 10px;
      text-align: center;
      font-weight: 700;
      font-size: 15px;
      box-shadow: 0 4px 14px rgba(229,57,53,0.3);
      transition: transform 0.2s, box-shadow 0.2s;
      width: auto;
      margin: 0;
      line-height: 1.4;
    }
    .blood-card:hover {
      transform: translateY(-3px) scale(1.04);
      box-shadow: 0 8px 22px rgba(229,57,53,0.45);
    }
    .blood-card .bg-type {
      font-size: 22px;
      font-weight: 800;
    }
    .blood-card .bg-units {
      font-size: 11px;
      opacity: 0.85;
      margin-top: 4px;
      font-weight: 500;
    }

    /* ── QUICK ACTIONS ── */
    .quick-actions {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 18px;
      margin-bottom: 48px;
    }
    .action-card {
      background: #fff;
      border-radius: 14px;
      padding: 28px 24px;
      text-align: center;
      text-decoration: none;
      color: #2c2c2c;
      box-shadow: 0 4px 16px rgba(0,0,0,0.06);
      transition: all 0.25s;
      border: 2px solid transparent;
    }
    .action-card:hover {
      border-color: #e53935;
      transform: translateY(-4px);
      box-shadow: 0 10px 28px rgba(183,28,28,0.14);
    }
    .action-card .ac-icon {
      font-size: 38px;
      margin-bottom: 14px;
    }
    .action-card h3 {
      font-size: 16px;
      font-weight: 700;
      color: #b71c1c;
      margin-bottom: 8px;
    }
    .action-card p {
      font-size: 13px;
      color: #777;
      line-height: 1.5;
    }

    /* ── FOOTER ── */
    footer {
      background: linear-gradient(135deg, #7b0000, #b71c1c);
      color: rgba(255,255,255,0.85);
      text-align: center;
      padding: 24px 20px;
      font-size: 13.5px;
    }
    footer a { color: #ffcdd2; text-decoration: none; }
    footer a:hover { text-decoration: underline; }

    /* ── RESPONSIVE ── */
    @media (max-width: 900px) {
      .sidebar { width: 70px; }
      .sidebar-logo span, .sidebar nav ul li a span { display: none; }
      .sidebar-logo img { width: 44px; height: 44px; }
      .main-content { margin-left: 70px; }
      .stats { grid-template-columns: repeat(2, 1fr); }
      .quick-actions { grid-template-columns: repeat(2, 1fr); }
      .hero-overlay { padding: 0 30px; }
      .hero-overlay h1 { font-size: 36px; }
      .page-body { padding: 28px 24px; }
    }

    /* ── DARK THEME OVERRIDES ── */
    .dark-theme body {
      background: #121212 !important;
      color: #e0e0e0 !important;
    }
    .dark-theme .page-body {
      background: #121212 !important;
    }
    .dark-theme .stats .card,
    .dark-theme .action-card {
      background: #1e1e1e !important;
      color: #f0f0f0 !important;
      border: 1px solid #333 !important;
      box-shadow: 0 4px 15px rgba(0,0,0,0.5) !important;
    }
    .dark-theme .stats .card strong,
    .dark-theme .section-title {
      color: #fff !important;
    }
    .dark-theme .stats .card .stat-label,
    .dark-theme .action-card p {
      color: #aaa !important;
    }
    .dark-theme .action-card h3 {
      color: #ff5252 !important;
    }
    .dark-theme .action-card:hover {
      border-color: #ff5252 !important;
      box-shadow: 0 10px 28px rgba(229,57,53,0.3) !important;
    }
    .dark-theme .blood-card {
      box-shadow: 0 4px 14px rgba(0,0,0,0.4) !important;
    }
  </style>
</head>
<body>

  <!-- ══ LEFT SIDEBAR ══ -->  <?php include 'sidebar.php'; ?>
  <!-- ══ MAIN CONTENT ══ -->
  <div class="main-content">

    <!-- Hero Banner -->
    <section class="hero">
      <img class="hero-bg" src="images/blood-banner.png" alt="Blood Donation Banner">
      <div class="hero-overlay">
        <p>Every drop counts. Join thousands of voluntary donors helping patients across India get the blood they urgently need.</p>
      </div>
    </section>

    <!-- Page Body -->
    <div class="page-body">

      <!-- Standalone Centered Buttons -->
      <div class="hero-actions-standalone">
        <a href="donor.php" class="btn-primary">🩸 Become a Donor</a>
        <a href="request_blood.php" class="btn-primary">📋 Request Blood</a>
      </div>

      <!-- Statistics -->
      <div class="section-title">📊 Live Statistics</div>
      <?php
        $conn = new mysqli("localhost", "root", "", "BDMS", 3307);
        if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

        $total_donors   = $conn->query("SELECT COUNT(*) AS c FROM Donar")->fetch_assoc()['c'];
        $total_units    = $conn->query("SELECT IFNULL(SUM(collection_quantity),0) AS s FROM collection")->fetch_assoc()['s'];
        $total_requests = $conn->query("SELECT COUNT(*) AS c FROM Requests")->fetch_assoc()['c'];
        $total_hospitals= $conn->query("SELECT COUNT(DISTINCT hospital) AS c FROM Requests")->fetch_assoc()['c'];

        echo "
        <div class='stats'>
          <div class='card'>
            <div class='stat-icon'>👥</div>
            <div class='stat-label'>Total Donors</div>
            <strong>$total_donors</strong>
          </div>
          <div class='card'>
            <div class='stat-icon'>🩸</div>
            <div class='stat-label'>Blood Units Available</div>
            <strong>$total_units</strong>
          </div>
          <div class='card'>
            <div class='stat-icon'>✅</div>
            <div class='stat-label'>Requests Completed</div>
            <strong>$total_requests</strong>
          </div>
          <div class='card'>
            <div class='stat-icon'>🏥</div>
            <div class='stat-label'>Hospitals Connected</div>
            <strong>$total_hospitals</strong>
          </div>
        </div>";

        // Blood Availability
        echo "<div class='availability'>";
        echo "<div class='section-title'>🩸 Blood Group Availability</div>";
        echo "<div class='blood-grid'>";
        $stock_query = $conn->query("SELECT blood_group, quantity FROM stock WHERE quantity > 0");
        if ($stock_query && $stock_query->num_rows > 0) {
            while ($row = $stock_query->fetch_assoc()) {
                echo "<div class='blood-card'>
                        <div class='bg-type'>" . htmlspecialchars($row['blood_group']) . "</div>
                        <div class='bg-units'>" . htmlspecialchars($row['quantity']) . " Units</div>
                      </div>";
            }
        } else {
            echo "<p style='color:#888;font-size:14px;'>No blood units currently available in stock.</p>";
        }
        echo "</div></div>";
        $conn->close();
      ?>

      <!-- Quick Actions -->
      <div class="section-title">⚡ Quick Actions</div>
      <div class="quick-actions">
        <a href="donor.php" class="action-card">
          <div class="ac-icon">🩸</div>
          <h3>Register as Donor</h3>
          <p>Sign up and help save lives by becoming a voluntary blood donor.</p>
        </a>
        <a href="search_donor.php" class="action-card">
          <div class="ac-icon">🔍</div>
          <h3>Search Donors</h3>
          <p>Find available donors in your city filtered by blood group.</p>
        </a>
        <a href="contact.php" class="action-card">
          <div class="ac-icon">📞</div>
          <h3>Emergency Contact</h3>
          <p>Reach our 24/7 helpdesk for urgent blood requirements.</p>
        </a>
      </div>

    </div><!-- /page-body -->

    <!-- Footer -->
    <footer>
      <p>📞 +91-9876543210 &nbsp;|&nbsp; ✉️ <a href="mailto:support@bdms.com">support@bdms.com</a></p>
      <p style="margin-top:6px; opacity:0.65;">© 2026 Blood Donation Management System. All Rights Reserved.</p>
    </footer>

  </div><!-- /main-content -->

</body>
</html>
