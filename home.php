<?php
include_once 'includes/auth.php';
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

    /* ── ADMIN CLICKABLE CARDS ── */
    .stats .card.admin-clickable {
      cursor: pointer;
    }
    .stats .card.admin-clickable:hover {
      transform: translateY(-6px) scale(1.02);
      box-shadow: 0 14px 36px rgba(183,28,28,0.22);
    }
    .card-click-hint {
      font-size: 10.5px;
      color: #b71c1c;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      margin-top: 10px;
      opacity: 0.7;
    }
    body.dark-theme .card-click-hint { color: #ff8a80; }

    /* ── DETAIL MODALS ── */
    .detail-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.65);
      backdrop-filter: blur(8px);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 4000;
      opacity: 0;
      visibility: hidden;
      transition: all 0.35s ease;
      padding: 20px;
    }
    .detail-overlay.active {
      opacity: 1;
      visibility: visible;
    }
    .detail-modal {
      background: #fff;
      border-radius: 20px;
      width: 100%;
      max-width: 900px;
      max-height: 85vh;
      display: flex;
      flex-direction: column;
      box-shadow: 0 24px 60px rgba(0,0,0,0.3);
      transform: translateY(30px) scale(0.97);
      transition: all 0.35s ease;
      overflow: hidden;
    }
    body.dark-theme .detail-modal {
      background: #1e1e1e;
      border: 1px solid #333;
    }
    .detail-overlay.active .detail-modal {
      transform: translateY(0) scale(1);
    }
    .detail-modal-header {
      background: linear-gradient(135deg, #b71c1c, #e53935);
      color: #fff;
      padding: 22px 28px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-shrink: 0;
    }
    .detail-modal-header h2 {
      font-size: 20px;
      font-weight: 800;
      margin: 0;
    }
    .detail-modal-close {
      background: rgba(255,255,255,0.2);
      border: none;
      color: #fff;
      width: 34px;
      height: 34px;
      border-radius: 50%;
      font-size: 20px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.2s;
      line-height: 1;
    }
    .detail-modal-close:hover { background: rgba(255,255,255,0.35); }
    .detail-modal-body {
      overflow-y: auto;
      flex: 1;
      padding: 24px 28px;
    }
    .detail-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }
    .detail-table thead th {
      background: #fef2f2;
      color: #b71c1c;
      font-weight: 700;
      padding: 12px 15px;
      text-align: left;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border-bottom: 2px solid #fde8e8;
    }
    body.dark-theme .detail-table thead th {
      background: #2a1a1a;
      color: #ff8a80;
      border-bottom-color: #3a1a1a;
    }
    .detail-table tbody td {
      padding: 12px 15px;
      border-bottom: 1px solid #f5f5f5;
      color: #444;
      vertical-align: middle;
    }
    body.dark-theme .detail-table tbody td {
      border-bottom-color: #2a2a2a;
      color: #ccc;
    }
    .detail-table tbody tr:hover td { background: #fff8f8; }
    body.dark-theme .detail-table tbody tr:hover td { background: rgba(229,57,53,0.06); }
    .badge-sm {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 700;
    }
    .badge-red  { background: rgba(183,28,28,0.1); color: #b71c1c; }
    .badge-green{ background: rgba(46,125,50,0.1);  color: #2e7d32; }
    .badge-blue { background: rgba(21,101,192,0.1);  color: #1565c0; }
    body.dark-theme .badge-red   { background: rgba(255,138,128,0.15); color: #ff8a80; }
    body.dark-theme .badge-green { background: rgba(102,187,106,0.15); color: #66bb6a; }
    body.dark-theme .badge-blue  { background: rgba(100,181,246,0.15); color: #64b5f6; }
    .hosp-card {
      background: #fef9f9;
      border: 1px solid #fde8e8;
      border-left: 4px solid #e53935;
      border-radius: 10px;
      padding: 16px 20px;
      margin-bottom: 14px;
    }
    body.dark-theme .hosp-card { background: #2a1818; border-color: #3a2020; border-left-color: #e53935; }
    .hosp-name { font-size: 16px; font-weight: 800; color: #b71c1c; margin-bottom: 6px; }
    body.dark-theme .hosp-name { color: #ff8a80; }
    .hosp-meta { font-size: 13px; color: #666; display: flex; gap: 18px; flex-wrap: wrap; }
    body.dark-theme .hosp-meta { color: #999; }
    .blood-unit-card {
      background: linear-gradient(135deg, #b71c1c, #e53935);
      color: #fff;
      border-radius: 14px;
      padding: 20px 16px;
      text-align: center;
    }
    .blood-unit-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
      gap: 14px;
    }
    .blood-unit-type { font-size: 26px; font-weight: 900; }
    .blood-unit-qty  { font-size: 14px; font-weight: 700; margin: 6px 0 2px; }
    .blood-unit-label{ font-size: 11px; opacity: 0.8; }
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
        $total_units    = $conn->query("SELECT IFNULL(SUM(quantity),0) AS s FROM stock")->fetch_assoc()['s'];
        $total_requests = $conn->query("SELECT COUNT(*) AS c FROM Requests")->fetch_assoc()['c'];
        $total_hospitals= $conn->query("SELECT COUNT(DISTINCT hospital) AS c FROM Requests")->fetch_assoc()['c'];

        $is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
        $clickable = $is_admin ? "admin-clickable" : "";
        $hint      = $is_admin ? "<div class='card-click-hint'>🔍 Click to View Details</div>" : "";

        echo "
        <div class='stats'>
          <div class='card $clickable' " . ($is_admin ? "onclick=\"openDetailModal('modal-donors')\"" : "") . ">
            <div class='stat-icon'>👥</div>
            <div class='stat-label'>Total Donors</div>
            <strong>$total_donors</strong>
            $hint
          </div>
          <div class='card $clickable' " . ($is_admin ? "onclick=\"openDetailModal('modal-blood')\"" : "") . ">
            <div class='stat-icon'>🩸</div>
            <div class='stat-label'>Blood Units Available</div>
            <strong>$total_units</strong>
            $hint
          </div>
          <div class='card $clickable' " . ($is_admin ? "onclick=\"openDetailModal('modal-requests')\"" : "") . ">
            <div class='stat-icon'>✅</div>
            <div class='stat-label'>Requests Completed</div>
            <strong>$total_requests</strong>
            $hint
          </div>
          <div class='card $clickable' " . ($is_admin ? "onclick=\"openDetailModal('modal-hospitals')\"" : "") . ">
            <div class='stat-icon'>🏥</div>
            <div class='stat-label'>Hospitals Connected</div>
            <strong>$total_hospitals</strong>
            $hint
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

        // ── Pre-fetch detail data for admin modals ──────────────────
        $donors_data    = [];
        $blood_data     = [];
        $requests_data  = [];
        $hospitals_data = [];

        if ($is_admin) {
            // 1. All donors + their total donations
            $dr = $conn->query("SELECT d.donar_id, d.donar_name, d.donar_age, d.donar_gender, d.donar_blood_group, d.donar_address, d.donar_contact, IFNULL(SUM(c.collection_quantity),0) AS total_donated, COUNT(c.collection_id) AS donations_count FROM Donar d LEFT JOIN Collection c ON d.donar_id = c.donar_id GROUP BY d.donar_id ORDER BY d.donar_id DESC");
            if ($dr) while ($r = $dr->fetch_assoc()) $donors_data[] = $r;

            // 2. All blood groups stock (including 0)
            $br = $conn->query("SELECT blood_group, quantity FROM stock ORDER BY blood_group");
            if ($br) while ($r = $br->fetch_assoc()) $blood_data[] = $r;

            // 3. All blood requests
            $rr = $conn->query("SELECT * FROM Requests ORDER BY request_id DESC");
            if ($rr) while ($r = $rr->fetch_assoc()) $requests_data[] = $r;

            // 4. Distinct hospitals with contact info
            $hr = $conn->query("SELECT DISTINCT hospital, contact, blood_group, SUM(units) AS total_units, COUNT(*) AS total_requests FROM Requests GROUP BY hospital ORDER BY hospital");
            if ($hr) while ($r = $hr->fetch_assoc()) $hospitals_data[] = $r;
        }

        $conn->close();
      ?>

      <!-- Quick Actions -->
      <div class="section-title">⚡ Quick Actions</div>
      <div class="quick-actions">
        <?php if ($_SESSION['role'] === 'donor'): ?>
          <a href="donor_history.php" class="action-card">
            <div class="ac-icon">📋</div>
            <h3>My History</h3>
            <p>View your past donations, recent transactions, and download certificates.</p>
          </a>
          <a href="notifications.php" class="action-card">
            <div class="ac-icon">🔔</div>
            <h3>Notifications</h3>
            <p>Check recent alerts, hospital request acceptances, and system updates.</p>
          </a>
          <a href="request_blood.php" class="action-card">
            <div class="ac-icon">🏥</div>
            <h3>Request Blood</h3>
            <p>Submit an urgent blood request for yourself or a patient.</p>
          </a>
        <?php else: ?>
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
        <?php endif; ?>
      </div>

      <!-- PREMIUM UPCOMING CAMPS / CAMPUS DRIVES -->
      <style>
        .dark-theme .camp-card-ticket {
          background: #1e1e1e !important;
          color: #f0f0f0 !important;
          border-color: #333 !important;
          border-left-color: #ff5252 !important;
          box-shadow: 0 4px 15px rgba(0,0,0,0.5) !important;
        }
        .dark-theme .camp-card-ticket h4 {
          color: #fff !important;
        }
        .dark-theme .camp-card-ticket p {
          color: #aaa !important;
        }
        .dark-theme .camp-card-ticket li {
          background: #2a1b1b !important;
          color: #ff8a80 !important;
          border: 1px solid rgba(255,255,255,0.05) !important;
        }
        .dark-theme .camp-card-ticket li span {
          color: #bbb !important;
        }
      </style>

      <div class="section-title" style="margin-top: 40px;">⛺ Upcoming Campus Blood Donation Drives</div>
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 48px;">
        
        <?php
          $conn = new mysqli("localhost", "root", "", "BDMS", 3307);
          if (!$conn->connect_error) {
              $conn->query("CREATE TABLE IF NOT EXISTS CampRegistrations (
                  id INT AUTO_INCREMENT PRIMARY KEY,
                  donor_id INT,
                  donor_name VARCHAR(150),
                  camp_location VARCHAR(255),
                  camp_date VARCHAR(100),
                  registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
              )");

              $camps = [
                  [
                      'date' => 'April 25, 2026',
                      'location' => 'KLE Hospital Auditorium, Hubli',
                      'desc' => 'A mega blood donation drive in collaboration with local NGOs. Comprehensive health screenings and donor certificates will be provided.'
                  ],
                  [
                      'date' => 'May 12, 2026',
                      'location' => 'BVB College Campus, Vidyanagar',
                      'desc' => 'Annual youth blood donation initiative. Special focus on educating first-time donors to help combat summer blood shortages across the state.'
                  ],
                  [
                      'date' => 'June 05, 2026',
                      'location' => 'Rotary Club Hall, Dharwad',
                      'desc' => 'Corporate and community donation event. Specialized health checkups, hemoglobin screening, and professional consultation provided completely free.'
                  ]
              ];

              $is_donor = (isset($_SESSION['role']) && $_SESSION['role'] === 'donor');
              $is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
              $u_id     = isset($_SESSION['donor_id']) ? intval($_SESSION['donor_id']) : 0;

              foreach ($camps as $camp) {
                  $c_loc  = $camp['location'];
                  $c_date = $camp['date'];
                  $c_desc = $camp['desc'];

                  echo "<div class='stats card camp-card-ticket' style='background: #fff; border-radius: 14px; padding: 24px; box-shadow: 0 4px 16px rgba(183,28,28,0.08); border-left: 5px solid #b71c1c; text-align: left; transition: transform 0.25s, box-shadow 0.25s; display: flex; flex-direction: column; justify-content: space-between; margin: 0; width: auto;'>";
                  echo "  <div>";
                  echo "    <div style='display: inline-block; padding: 5px 12px; background: rgba(183,28,28,0.08); color: #b71c1c; border-radius: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;'>$c_date</div>";
                  echo "    <h4 style='font-size: 18px; font-weight: 800; color: #333; margin-bottom: 8px;'>$c_loc</h4>";
                  echo "    <p style='font-size: 13.5px; color: #666; line-height: 1.5; margin-bottom: 16px;'>$c_desc</p>";
                  echo "  </div>";

                  if ($is_donor) {
                      // Check if already registered
                      $check = $conn->query("SELECT id FROM CampRegistrations WHERE donor_id = $u_id AND camp_location = '" . $conn->real_escape_string($c_loc) . "' LIMIT 1");
                      $is_reg = ($check && $check->num_rows > 0);

                      echo "  <div style='margin-top: 10px;'>";
                      if ($is_reg) {
                          echo "    <div class='badge-green' style='display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; padding: 8px 16px; background: rgba(46,125,50,0.1); color: #2e7d32; border-radius: 8px;'>✓ Registered (Participating)</div>";
                      } else {
                          echo "    <button onclick=\"registerCamp(this, '" . htmlspecialchars($c_loc) . "', '" . htmlspecialchars($c_date) . "')\" style='padding: 10px 18px; background: linear-gradient(135deg, #e53935, #b71c1c); color: #fff; border: none; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer; box-shadow: 0 4px 12px rgba(229,57,53,0.25); display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;'>🎟️ Register for Drive</button>";
                      }
                      echo "  </div>";
                  } elseif ($is_admin) {
                      // Admin participant list check
                      $regs = $conn->query("SELECT donor_name, registered_at FROM CampRegistrations WHERE camp_location = '" . $conn->real_escape_string($c_loc) . "' ORDER BY registered_at DESC");
                      echo "  <div style='margin-top: 15px; border-top: 1px dashed rgba(0,0,0,0.1); padding-top: 12px;'>";
                      echo "    <div style='font-size: 11px; font-weight: 700; color: #888; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;'>👥 Registered Participants (" . ($regs ? $regs->num_rows : 0) . ")</div>";
                      if ($regs && $regs->num_rows > 0) {
                          echo "    <ul style='list-style: none; padding: 0; margin: 0; max-height: 120px; overflow-y: auto;'>";
                          while ($r = $regs->fetch_assoc()) {
                              $reg_time = date('M j, g:i a', strtotime($r['registered_at']));
                              echo "      <li style='font-size: 13px; color: #1565c0; font-weight: 600; display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; background: #f0f7ff; padding: 6px 10px; border-radius: 6px;'>";
                              echo "        <span>👤 " . htmlspecialchars($r['donor_name']) . "</span>";
                              echo "        <span style='font-size: 10px; color: #888; font-weight: 400;'>$reg_time</span>";
                              echo "      </li>";
                          }
                          echo "    </ul>";
                      } else {
                          echo "    <p style='font-size: 12px; color: #aaa; font-style: italic; margin: 0;'>No participants registered yet.</p>";
                      }
                      echo "  </div>";
                  }

                  echo "</div>";
              }
              $conn->close();
          }
        ?>

      </div>

    </div><!-- /page-body -->

    <!-- Footer -->
    <footer>
      <p>📞 +91-9876543210 &nbsp;|&nbsp; ✉️ <a href="mailto:support@bdms.com">support@bdms.com</a></p>
      <p style="margin-top:6px; opacity:0.65;">© 2026 Blood Donation Management System. All Rights Reserved.</p>
    </footer>

  </div><!-- /main-content -->

<?php if ($is_admin): ?>

<!-- ═══════════════════════════════════════════
     MODAL 1: ALL DONORS
═══════════════════════════════════════════ -->
<div class="detail-overlay" id="modal-donors">
  <div class="detail-modal">
    <div class="detail-modal-header">
      <h2>👥 All Registered Donors</h2>
      <button class="detail-modal-close" onclick="closeDetailModal('modal-donors')">✕</button>
    </div>
    <div class="detail-modal-body">
      <table class="detail-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Age / Gender</th>
            <th>Blood Group</th>
            <th>Location</th>
            <th>Contact</th>
            <th>Donations</th>
            <th>Total Donated</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($donors_data)): ?>
            <tr><td colspan="8" style="text-align:center;padding:30px;color:#aaa;">No donors found.</td></tr>
          <?php else: foreach ($donors_data as $i => $d): ?>
            <tr>
              <td><?= $i+1 ?></td>
              <td><strong><?= htmlspecialchars($d['donar_name']) ?></strong></td>
              <td><?= htmlspecialchars($d['donar_age']) ?> / <?= htmlspecialchars($d['donar_gender']) ?></td>
              <td><span class="badge-sm badge-red"><?= htmlspecialchars($d['donar_blood_group']) ?></span></td>
              <td><?= htmlspecialchars($d['donar_address']) ?></td>
              <td><?= htmlspecialchars($d['donar_contact']) ?></td>
              <td><span class="badge-sm badge-blue"><?= $d['donations_count'] ?> times</span></td>
              <td><span class="badge-sm badge-green"><?= number_format($d['total_donated']) ?> ml</span></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════
     MODAL 2: BLOOD UNITS
═══════════════════════════════════════════ -->
<div class="detail-overlay" id="modal-blood">
  <div class="detail-modal">
    <div class="detail-modal-header">
      <h2>🩸 Blood Unit Availability</h2>
      <button class="detail-modal-close" onclick="closeDetailModal('modal-blood')">✕</button>
    </div>
    <div class="detail-modal-body">
      <?php if (empty($blood_data)): ?>
        <p style="text-align:center;color:#aaa;padding:30px;">No stock data available.</p>
      <?php else: ?>
        <div class="blood-unit-grid">
          <?php foreach ($blood_data as $b): ?>
            <div class="blood-unit-card">
              <div class="blood-unit-type"><?= htmlspecialchars($b['blood_group']) ?></div>
              <div class="blood-unit-qty"><?= number_format($b['quantity']) ?> ml</div>
              <div class="blood-unit-label">Available in Stock</div>
            </div>
          <?php endforeach; ?>
        </div>
        <p style="margin-top:24px;font-size:13px;color:#888;text-align:center;">Stock is updated automatically after each donation and issuance.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════
     MODAL 3: REQUESTS
═══════════════════════════════════════════ -->
<div class="detail-overlay" id="modal-requests">
  <div class="detail-modal">
    <div class="detail-modal-header">
      <h2>✅ All Blood Requests</h2>
      <button class="detail-modal-close" onclick="closeDetailModal('modal-requests')">✕</button>
    </div>
    <div class="detail-modal-body">
      <table class="detail-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Patient Name</th>
            <th>Blood Group</th>
            <th>Units Needed</th>
            <th>Hospital</th>
            <th>Contact</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($requests_data)): ?>
            <tr><td colspan="6" style="text-align:center;padding:30px;color:#aaa;">No requests found.</td></tr>
          <?php else: foreach ($requests_data as $i => $req): ?>
            <tr>
              <td><?= $i+1 ?></td>
              <td><strong><?= htmlspecialchars($req['patient_name']) ?></strong></td>
              <td><span class="badge-sm badge-red"><?= htmlspecialchars($req['blood_group']) ?></span></td>
              <td><span class="badge-sm badge-blue"><?= htmlspecialchars($req['units']) ?> units</span></td>
              <td><?= htmlspecialchars($req['hospital']) ?></td>
              <td><?= htmlspecialchars($req['contact']) ?></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════
     MODAL 4: HOSPITALS
═══════════════════════════════════════════ -->
<div class="detail-overlay" id="modal-hospitals">
  <div class="detail-modal">
    <div class="detail-modal-header">
      <h2>🏥 Connected Hospitals</h2>
      <button class="detail-modal-close" onclick="closeDetailModal('modal-hospitals')">✕</button>
    </div>
    <div class="detail-modal-body">
      <?php if (empty($hospitals_data)): ?>
        <p style="text-align:center;color:#aaa;padding:30px;">No hospital records found.</p>
      <?php else: foreach ($hospitals_data as $h): ?>
        <div class="hosp-card">
          <div class="hosp-name">🏥 <?= htmlspecialchars($h['hospital']) ?></div>
          <div class="hosp-meta">
            <span>📞 <?= htmlspecialchars($h['contact']) ?></span>
            <span>🩸 Blood Group Requested: <strong><?= htmlspecialchars($h['blood_group']) ?></strong></span>
            <span>📋 Total Requests: <strong><?= htmlspecialchars($h['total_requests']) ?></strong></span>
            <span>💧 Total Units Needed: <strong><?= number_format($h['total_units']) ?></strong></span>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>
</div>

<script>
  function openDetailModal(id) {
    document.getElementById(id).classList.add('active');
    document.body.style.overflow = 'hidden';
  }
  function closeDetailModal(id) {
    document.getElementById(id).classList.remove('active');
    document.body.style.overflow = '';
  }
  // Close on backdrop click
  document.querySelectorAll('.detail-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
      if (e.target === this) {
        this.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
  });
  // Close on Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.detail-overlay.active').forEach(m => {
        m.classList.remove('active');
        document.body.style.overflow = '';
      });
    }
  });

  // Inject premium toast styles dynamically
  const toastStyle = document.createElement('style');
  toastStyle.innerHTML = `
    .premium-toast {
      position: fixed;
      bottom: 24px;
      right: 24px;
      background: #ffffff;
      color: #333333;
      padding: 16px 24px;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.15);
      z-index: 9999;
      display: flex;
      align-items: center;
      gap: 12px;
      font-family: 'Outfit', sans-serif;
      font-size: 14.5px;
      font-weight: 600;
      transform: translateY(100px);
      opacity: 0;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      border-left: 5px solid #2e7d32;
    }
    .premium-toast.active {
      transform: translateY(0);
      opacity: 1;
    }
    .premium-toast.error {
      border-left-color: #e53935;
    }
    body.dark-theme .premium-toast {
      background: #1e1e1e;
      color: #ffffff;
      box-shadow: 0 10px 30px rgba(0,0,0,0.4);
    }
  `;
  document.head.appendChild(toastStyle);

  function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = 'premium-toast' + (type === 'error' ? ' error' : '');
    toast.innerHTML = `
      <span>${type === 'error' ? '❌' : '🎉'}</span>
      <span>${message}</span>
    `;
    document.body.appendChild(toast);
    
    // Trigger animation
    setTimeout(() => toast.classList.add('active'), 50);
    
    // Remove after 4 seconds
    setTimeout(() => {
      toast.classList.remove('active');
      setTimeout(() => toast.remove(), 400);
    }, 4000);
  }

  // Register for Donation Drive Camp
  function registerCamp(btn, location, date) {
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '🔄 Registering...';
    
    const formData = new URLSearchParams();
    formData.append('camp_location', location);
    formData.append('camp_date', date);
    
    fetch('register_camp.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: formData.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Dynamically replace the button with the green success badge
            const container = btn.parentElement;
            container.innerHTML = `<div class='badge-green' style='display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; padding: 8px 16px; background: rgba(46,125,50,0.1); color: #2e7d32; border-radius: 8px;'>✓ Registered (Participating)</div>`;
            showToast('success', (data.msg || 'Successfully registered for the drive!') + ' <a href="notifications.php" style="color: inherit; text-decoration: underline; margin-left: 8px;">View Notification</a>');
        } else {
            btn.disabled = false;
            btn.innerHTML = originalText;
            showToast('error', data.msg || 'Failed to register.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.disabled = false;
        btn.innerHTML = originalText;
        showToast('error', 'A server connection error occurred. Please try again.');
    });
  }
</script>

<?php endif; ?>

</body>
</html>
