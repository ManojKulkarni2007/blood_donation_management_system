<?php
include_once 'includes/auth.php';
if ($_SESSION['role'] !== 'donor') {
    header("Location: home.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "BDMS", 3307);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$donor_id = intval($_SESSION['donor_id']);
$sql = "SELECT donar_name, donar_age, donar_blood_group, donar_address FROM Donar WHERE donar_id = $donor_id";
$result = $conn->query($sql);
$donor = $result ? $result->fetch_assoc() : null;

if (!$donor) {
    // Edge case if donor is not registered properly yet
    $donor = [
        'donar_name' => $_SESSION['donor_name'],
        'donar_age' => 'N/A',
        'donar_blood_group' => 'N/A',
        'donar_address' => 'N/A'
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My History - Blood Donation Management System</title>
  <link rel="stylesheet" href="style.css?v=6">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <style>
    .profile-card {
      background: linear-gradient(135deg, #b71c1c, #e53935);
      color: #fff;
      border-radius: 16px;
      padding: 30px;
      margin-bottom: 40px;
      box-shadow: 0 10px 30px rgba(183,28,28,0.2);
      display: flex;
      align-items: center;
      gap: 30px;
    }
    body.dark-theme .profile-card {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    .profile-avatar {
      font-size: 64px;
      background: rgba(255,255,255,0.2);
      width: 100px;
      height: 100px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
    }
    .profile-details h2 {
      font-size: 28px;
      font-weight: 800;
      margin-bottom: 10px;
      letter-spacing: 0.5px;
    }
    body.dark-theme .profile-details h2 {
        color: var(--text-main);
    }
    .profile-meta {
      display: flex;
      gap: 20px;
      font-size: 15px;
      font-weight: 600;
      opacity: 0.9;
    }
    body.dark-theme .profile-meta {
        color: var(--text-muted);
    }
    
    .table-container {
      background: #fff;
      border-radius: 16px;
      padding: 20px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }
    body.dark-theme .table-container {
      background: var(--bg-card);
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th {
      text-align: left;
      padding: 15px;
      border-bottom: 2px solid #eee;
      color: #777;
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    body.dark-theme th {
      border-bottom-color: var(--border-color);
      color: #999;
    }
    td {
      padding: 15px;
      border-bottom: 1px solid #f5f5f5;
      font-size: 14.5px;
      vertical-align: middle;
    }
    body.dark-theme td {
      border-bottom-color: var(--border-color);
      color: var(--text-main);
    }
    .btn-cert {
      background: #e53935;
      color: #fff;
      border: none;
      padding: 8px 16px;
      border-radius: 8px;
      font-weight: 700;
      font-size: 12px;
      cursor: pointer;
      transition: all 0.2s;
    }
    .btn-cert:hover {
      background: #b71c1c;
      transform: translateY(-2px);
    }
    .no-data {
      text-align: center;
      padding: 40px;
      color: #aaa;
    }

    /* PDF template offscreen container */
    #certificate-container {
      position: absolute;
      left: -9999px;
      top: -9999px;
    }

    /* Eligibility Overlay Modal for History Certificate Viewer */
    .eligibility-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.7);
      backdrop-filter: blur(8px);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 5000;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
      overflow-y: auto;
      padding: 20px 0;
    }
  </style>
</head>
<body>

  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <div class="page-header">
      <a href="home.php" class="back-link">← Back to Dashboard</a>
      <h1>📋 My Profile & History</h1>
    </div>

    <div class="page-body">
      
      <!-- Profile Card -->
      <div class="profile-card">
        <div class="profile-avatar">👤</div>
        <div class="profile-details">
          <h2><?= htmlspecialchars($donor['donar_name']) ?></h2>
          <div class="profile-meta">
            <span><strong>Age:</strong> <?= htmlspecialchars($donor['donar_age']) ?></span>
            <span><strong>Blood Group:</strong> <?= htmlspecialchars($donor['donar_blood_group']) ?></span>
            <span><strong>Location:</strong> <?= htmlspecialchars($donor['donar_address']) ?></span>
          </div>
        </div>
      </div>

      <!-- History Table -->
      <h3 style="margin-bottom: 20px; color: #b71c1c;">🩸 Recent Donations</h3>
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Date</th>
              <th>Location</th>
              <th>Amount (ml)</th>
              <th>Certificate</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $sql2 = "SELECT collection_id, collection_date, collection_quantity 
                       FROM Collection 
                       WHERE donar_id = $donor_id 
                       ORDER BY collection_date DESC";
              $res2 = $conn->query($sql2);
              if ($res2 && $res2->num_rows > 0) {
                  while ($row = $res2->fetch_assoc()) {
                      echo "<tr>
                              <td><strong>" . htmlspecialchars($row['collection_date']) . "</strong></td>
                              <td>" . htmlspecialchars($donor['donar_address']) . "</td>
                              <td>" . htmlspecialchars($row['collection_quantity']) . " ml</td>
                              <td>
                                <button class='btn-cert' onclick='showCertModal(\"" . htmlspecialchars($donor['donar_name']) . "\", \"" . htmlspecialchars($donor['donar_age']) . "\", \"" . htmlspecialchars($donor['donar_blood_group']) . "\", \"" . htmlspecialchars($row['collection_quantity']) . "\", \"" . htmlspecialchars($row['collection_date']) . "\", \"" . htmlspecialchars($row['collection_id']) . "\")'>👁️ View</button>
                              </td>
                            </tr>";
                  }
              } else {
                  echo "<tr><td colspan='4' class='no-data'>No donation history found yet.</td></tr>";
              }
            ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>

  <!-- DYNAMIC CERTIFICATE OVERLAY VIEWER MODAL -->
  <div class="eligibility-overlay" id="certOverlay" style="position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 5000; opacity: 0; visibility: hidden; transition: all 0.3s ease; overflow-y: auto; padding: 20px 0;">
    <div style="transform: scale(0.9); transition: all 0.3s ease; max-width: 760px; width: 95%; display: flex; flex-direction: column; align-items: center; gap: 20px; outline: none;" id="certModalContainer">
      
      <!-- Close button -->
      <div style="width: 100%; display: flex; justify-content: flex-end;">
        <button onclick="closeCertModal()" style="background: rgba(255,255,255,0.2); border: 2px solid #fff; font-size: 18px; width: 36px; height: 36px; border-radius: 50%; color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">✕</button>
      </div>

      <!-- VISIBLE INTERACTIVE CERTIFICATE -->
      <div id="pdf-certificate" style="width: 700px; min-width: 700px; padding: 40px; font-family: 'Outfit', 'Inter', sans-serif; border: 8px double #b71c1c; background: #ffffff; color: #111111; box-sizing: border-box; position: relative; text-align: left; box-shadow: 0 8px 30px rgba(0,0,0,0.3); border-radius: 4px;">
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
              <p style="margin: 0;"><strong>Certificate ID:</strong> <span id="pdf_cert_id">DONXYZ1234</span></p>
              <p style="margin: 2px 0 0 0;"><strong>Verification Code:</strong> <span id="pdf_cert_code">BDMS-APPROVED-A1B2</span></p>
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

      <!-- Action buttons -->
      <div style="display: flex; gap: 14px; width: 100%; justify-content: center;">
        <button onclick="downloadCertPDF()" style="padding: 14px 28px; background: linear-gradient(135deg, #b71c1c, #e53935); color: #fff; font-weight: 700; border: none; border-radius: 10px; cursor: pointer; box-shadow: 0 4px 15px rgba(183,28,28,0.25); display: flex; align-items: center; gap: 8px;">
          📥 Download Certificate (PDF)
        </button>
        <button onclick="closeCertModal()" style="padding: 14px 28px; background: #fff; color: #333; font-weight: 700; border: 1.5px solid #ccc; border-radius: 10px; cursor: pointer;">
          Close Window
        </button>
      </div>

    </div>
  </div>

  <script src="theme.js"></script>
  <script>
    let activeCertName = "";
    function showCertModal(name, age, bg, amt, date, cid) {
      const overlay = document.getElementById('certOverlay');
      const container = document.getElementById('certModalContainer');

      activeCertName = name.replace(/\s+/g, '_') + '_Donation_Certificate';

      document.getElementById('pdf_donor_name').textContent = name;
      document.getElementById('pdf_donor_age').textContent = age;
      document.getElementById('pdf_donor_group').textContent = bg;
      document.getElementById('pdf_donor_amount').textContent = amt + ' ml';
      document.getElementById('pdf_donation_date').textContent = date;
      
      const paddedId = String(cid).padStart(4, '0');
      const namePart = name.replace(/[^a-zA-Z]/g, '').padEnd(3, 'X').substring(0, 3).toUpperCase();
      const finalCertId = `DON${namePart}${paddedId}`;
      document.getElementById('pdf_cert_id').textContent = finalCertId;
      
      // Calculate a deterministic verification code based on certificate parameters
      const hash = (finalCertId.charCodeAt(3) + finalCertId.charCodeAt(4)) % 100;
      document.getElementById('pdf_cert_code').textContent = `BDMS-APPROVED-V${hash}X`;

      overlay.style.opacity = '1';
      overlay.style.visibility = 'visible';
      container.style.transform = 'scale(1)';
    }

    function closeCertModal() {
      const overlay = document.getElementById('certOverlay');
      const container = document.getElementById('certModalContainer');
      overlay.style.opacity = '0';
      overlay.style.visibility = 'hidden';
      container.style.transform = 'scale(0.9)';
    }

    function downloadCertPDF() {
      const element = document.getElementById('pdf-certificate');
      const opt = {
        margin:       10,
        filename:     activeCertName + '.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2.5, useCORS: true },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
      };
      html2pdf().from(element).set(opt).save();
    }

    // Close overlay when clicking outside
    document.getElementById('certOverlay').addEventListener('click', function(e) {
      if (e.target === this) {
        closeCertModal();
      }
    });
  </script>
</body>
</html>
