<?php
include_once 'includes/auth.php';
if ($_SESSION['role'] !== 'donor' && $_SESSION['role'] !== 'admin') {
    header("Location: home.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "BDMS", 3307);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$donor_id = intval($_SESSION['donor_id']);

// Ensure table exists just in case
$conn->query("CREATE TABLE IF NOT EXISTS Notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_request_acceptance BOOLEAN DEFAULT FALSE
)");

// Auto-update schema to support next donation scheduling
$check_col = $conn->query("SHOW COLUMNS FROM Notifications LIKE 'is_donation_schedule'");
if ($check_col && $check_col->num_rows == 0) {
    $conn->query("ALTER TABLE Notifications ADD is_donation_schedule BOOLEAN DEFAULT FALSE");
    $conn->query("ALTER TABLE Notifications ADD donation_date VARCHAR(100) DEFAULT NULL");
    $conn->query("ALTER TABLE Notifications ADD donation_location TEXT DEFAULT NULL");
}

if ($_SESSION['role'] === 'donor') {
    // Process simulated 60s acceptances
    $sql = "SELECT id, created_at FROM Notifications WHERE donor_id = $donor_id AND is_request_acceptance = 1";
    $res = $conn->query($sql);
    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $time_diff = time() - strtotime($row['created_at']);
            if ($time_diff > 60) {
                // Check if we already inserted the acceptance for this request
                $req_id = $row['id'];
                $check = $conn->query("SELECT id FROM Notifications WHERE donor_id = $donor_id AND message LIKE 'Hospital accepted request%' AND is_request_acceptance = 0 AND created_at >= '{$row['created_at']}'");
                if ($check && $check->num_rows == 0) {
                    // Insert the simulated acceptance
                    $msg = "Hospital accepted request and allowing you to take the blood as soon as possible.";
                    $conn->query("INSERT INTO Notifications (donor_id, message, is_request_acceptance) VALUES ($donor_id, '$msg', 0)");
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notifications - Blood Donation Management System</title>
  <link rel="stylesheet" href="style.css?v=6">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <!-- html2pdf library for beautiful client-side PDF generation of certificates -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <style>
    .notif-container {
      max-width: 800px;
      margin: 0 auto;
    }
    .notif-card {
      background: #fff;
      border-radius: 12px;
      padding: 20px 24px;
      margin-bottom: 16px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
      border-left: 5px solid #e53935;
      display: flex;
      align-items: flex-start;
      gap: 16px;
      transition: transform 0.2s;
    }
    .notif-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }
    body.dark-theme .notif-card {
      background: var(--bg-card);
      border-color: #ff5252;
    }
    .notif-icon {
      font-size: 28px;
      background: rgba(229,57,53,0.1);
      width: 50px;
      height: 50px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      flex-shrink: 0;
    }
    .notif-card.success .notif-icon {
      background: rgba(46,125,50,0.1);
    }
    .notif-card.success {
      border-left-color: #2e7d32;
    }
    .notif-content {
      flex: 1;
    }
    .notif-msg {
      font-size: 15px;
      color: #333;
      line-height: 1.5;
      margin-bottom: 8px;
      font-weight: 500;
    }
    body.dark-theme .notif-msg {
      color: var(--text-main);
    }
    .notif-time {
      font-size: 12px;
      color: #888;
      font-weight: 600;
    }
    .no-notifs {
      text-align: center;
      padding: 60px 20px;
      background: #fff;
      border-radius: 16px;
      color: #aaa;
    }
    body.dark-theme .no-notifs {
      background: var(--bg-card);
    }
    .no-notifs .icon {
      font-size: 48px;
      margin-bottom: 15px;
    }

    /* Scheduled donation styles */
    .notif-card.schedule {
      border-left-color: #b71c1c;
      background: #fdf5f5;
    }
    body.dark-theme .notif-card.schedule {
      background: #231616;
      border-left-color: #ff5252;
    }
    .notif-card.schedule:hover {
      box-shadow: 0 8px 25px rgba(183,28,28,0.15);
      transform: translateY(-3px);
    }

    /* Admin registration notification styles */
    .notif-card.admin-info {
      border-left-color: #1565c0;
      background: #f0f7ff;
    }
    body.dark-theme .notif-card.admin-info {
      background: #121e2d;
      border-left-color: #64b5f6;
    }
    .notif-card.admin-info:hover {
      box-shadow: 0 8px 25px rgba(21,101,192,0.15);
      transform: translateY(-3px);
    }

    /* Certificate notification styles */
    .notif-card.certificate {
      border-left-color: #d4af37;
      background: #fdfaf0;
    }
    body.dark-theme .notif-card.certificate {
      background: #252116;
      border-left-color: #ffca28;
    }
    .notif-card.certificate:hover {
      box-shadow: 0 8px 25px rgba(212,175,55,0.15);
      transform: translateY(-3px);
    }

    /* Camp registration notification styles */
    .notif-card.camp-reg {
      border-left-color: #2e7d32;
      background: #f4faf4;
    }
    body.dark-theme .notif-card.camp-reg {
      background: #172417;
      border-left-color: #81c784;
    }
    .notif-card.camp-reg:hover {
      box-shadow: 0 8px 25px rgba(46,125,50,0.15);
      transform: translateY(-3px);
    }
  </style>
</head>
<body>

  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <div class="page-header">
      <a href="home.php" class="back-link">← Back to Dashboard</a>
      <h1>🔔 Notifications</h1>
      <p>Stay updated with your blood requests and donation alerts.</p>
    </div>

    <div class="page-body">
      <div class="notif-container">
        <?php
          $sql_notifs = "SELECT * FROM Notifications WHERE donor_id = $donor_id ORDER BY created_at DESC";
          $notifs = $conn->query($sql_notifs);

          if ($notifs && $notifs->num_rows > 0) {
              while ($notif = $notifs->fetch_assoc()) {
                  $is_sched = isset($notif['is_donation_schedule']) && $notif['is_donation_schedule'];
                  $is_cert  = isset($notif['is_certificate']) && $notif['is_certificate'];
                  $is_admin_notif = ($_SESSION['role'] === 'admin' && (strpos($notif['message'], 'registered to donate blood') !== false || strpos($notif['message'], 'registered for the donation camp') !== false));
                  $is_camp_reg = ($_SESSION['role'] === 'donor' && strpos($notif['message'], 'donation camp') !== false);
                  $is_success = strpos($notif['message'], 'accepted') !== false || strpos($notif['message'], 'successfully') !== false;
                  
                  $card_class = 'notif-card';
                  if ($is_sched) {
                      $card_class .= ' schedule';
                      $icon = '📅';
                  } elseif ($is_cert) {
                      $card_class .= ' certificate';
                      $icon = '🏆';
                  } elseif ($is_admin_notif) {
                      $card_class .= ' admin-info';
                      $icon = '👤';
                  } elseif ($is_camp_reg) {
                      $card_class .= ' camp-reg';
                      $icon = '🎟️';
                  } elseif ($is_success) {
                      $card_class .= ' success';
                      $icon = '✅';
                  } else {
                      $icon = 'ℹ️';
                  }
                  
                  // Format time
                  $time_str = date("F j, Y, g:i a", strtotime($notif['created_at']));

                  if ($is_sched) {
                      $esc_date = htmlspecialchars($notif['donation_date'] ?? '');
                      $esc_loc  = htmlspecialchars($notif['donation_location'] ?? '');
                      echo "<div class='$card_class' onclick=\"showLocationModal('$esc_date', '$esc_loc')\" style='cursor: pointer;'>
                              <div class='notif-icon'>$icon</div>
                              <div class='notif-content'>
                                <div class='notif-badge' style='background: #e53935; color: #fff; font-size: 10px; font-weight: 800; padding: 3px 10px; border-radius: 12px; display: inline-block; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;'>📍 Next Blood Donation Slot</div>
                                <div class='notif-msg' style='font-weight: 700; color: #b71c1c;'>" . htmlspecialchars($notif['message']) . "</div>
                                <div class='notif-click-hint' style='font-size: 12.5px; color: #1565c0; font-weight: 600; margin-top: 6px; display: flex; align-items: center; gap: 4px;'>🔍 Click to view assigned donation location & details</div>
                                <div class='notif-time' style='margin-top: 8px;'>$time_str</div>
                              </div>
                            </div>";
                  } elseif ($is_camp_reg) {
                      echo "<div class='$card_class'>
                              <div class='notif-icon'>$icon</div>
                              <div class='notif-content'>
                                <div class='notif-badge' style='background: #2e7d32; color: #fff; font-size: 10px; font-weight: 800; padding: 3px 10px; border-radius: 12px; display: inline-block; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;'>⛺ Campus Donation Camp</div>
                                <div class='notif-msg' style='font-weight: 700; color: #1b5e20;'>" . htmlspecialchars($notif['message']) . "</div>
                                <div class='notif-time' style='margin-top: 8px;'>$time_str</div>
                              </div>
                            </div>";
                  } elseif ($is_cert) {
                      $esc_cname = htmlspecialchars($notif['cert_donor_name'] ?? '');
                      $esc_cage  = intval($notif['cert_donor_age'] ?? 0);
                      $esc_cbg   = htmlspecialchars($notif['cert_donor_blood_group'] ?? '');
                      $esc_camt  = intval($notif['cert_donated_amount'] ?? 350);
                      $esc_cdate = htmlspecialchars($notif['cert_donation_date'] ?? '');
                      $esc_cid   = htmlspecialchars($notif['cert_id'] ?? '');
                      $esc_ccode = htmlspecialchars($notif['cert_code'] ?? '');

                      echo "<div class='$card_class' onclick=\"showCertificateModal('$esc_cname', $esc_cage, '$esc_cbg', $esc_camt, '$esc_cdate', '$esc_cid', '$esc_ccode')\" style='cursor: pointer;'>
                              <div class='notif-icon'>$icon</div>
                              <div class='notif-content'>
                                <div class='notif-badge' style='background: #d4af37; color: #111; font-size: 10px; font-weight: 800; padding: 3px 10px; border-radius: 12px; display: inline-block; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;'>🏆 Blood Donation Certificate</div>
                                <div class='notif-msg' style='font-weight: 700; color: #856404;'>" . htmlspecialchars($notif['message']) . "</div>
                                <div class='notif-click-hint' style='font-size: 12.5px; color: #2e7d32; font-weight: 600; margin-top: 6px; display: flex; align-items: center; gap: 4px;'>🔍 Click to view and download your official certificate (PDF)</div>
                                <div class='notif-time' style='margin-top: 8px;'>$time_str</div>
                              </div>
                            </div>";
                  } elseif ($is_admin_notif) {
                      $is_proc = isset($notif['is_processed']) && $notif['is_processed'];
                      $msg_text = $notif['message'];
                      $parsed_name = 'Donor';
                      if (preg_match('/Donor (.*?) is registered/', $msg_text, $matches)) {
                          $parsed_name = $matches[1];
                      }
                      $esc_parsed_name = htmlspecialchars($parsed_name);
                      $ref_donor_id = intval($notif['ref_donor_id'] ?? 0);
                      $notif_id = intval($notif['id'] ?? 0);

                      echo "<div class='$card_class'>
                              <div class='notif-icon'>$icon</div>
                              <div class='notif-content'>
                                <div class='notif-badge' style='background: #1565c0; color: #fff; font-size: 10px; font-weight: 800; padding: 3px 10px; border-radius: 12px; display: inline-block; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;'>👤 New Donor Registration</div>
                                <div class='notif-msg' style='font-weight: 600; color: #0d47a1;'>" . htmlspecialchars($notif['message']) . "</div>";
                      if ($is_proc) {
                          echo "<div style='display:inline-flex; align-items:center; gap:6px; color:#2e7d32; font-weight:700; font-size:13px; margin-top:10px;'>
                                  <span style='background:rgba(46,125,50,0.1); width:22px; height:22px; display:inline-flex; align-items:center; justify-content:center; border-radius:50%;'>✓</span> Certificate Issued &amp; Sent
                                </div>";
                      } else {
                          echo "<button onclick=\"openIssueModal($ref_donor_id, $notif_id, '$esc_parsed_name')\" style='margin-top: 12px; padding: 8px 16px; background: linear-gradient(135deg, #1565c0, #0d47a1); color: #fff; border: none; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(21,101,192,0.25); transition: all 0.2s;'>🏆 Send Certificate</button>";
                      }
                      echo "    <div class='notif-time' style='margin-top: 8px;'>$time_str</div>
                              </div>
                            </div>";
                  } else {
                      echo "<div class='$card_class'>
                              <div class='notif-icon'>$icon</div>
                              <div class='notif-content'>
                                <div class='notif-msg'>" . htmlspecialchars($notif['message']) . "</div>
                                <div class='notif-time'>$time_str</div>
                              </div>
                            </div>";
                  }
              }
          } else {
              echo "<div class='no-notifs'>
                      <div class='icon'>📭</div>
                      <p>You have no new notifications.</p>
                    </div>";
          }
          $conn->close();
        ?>
      </div>
    </div>
  </div>

  <!-- LOCATION MODAL -->
  <div class="eligibility-overlay" id="locationOverlay" style="position: fixed; inset: 0; background: rgba(0,0,0,0.65); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 4000; opacity: 0; visibility: hidden; transition: all 0.3s ease;">
    <div class="eligibility-modal" style="background: #fff; border-radius: 20px; padding: 36px; max-width: 520px; width: 90%; box-shadow: 0 20px 50px rgba(0,0,0,0.3); text-align: left; transform: scale(0.9); transition: all 0.3s ease; position: relative;">
      
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; border-bottom: 2px solid #feeef0; padding-bottom: 12px;">
        <h2 style="font-size: 22px; font-weight: 800; color: #b71c1c; margin: 0; display: flex; align-items: center; gap: 10px;">
          <span>📍</span> Donation Venue Details
        </h2>
        <button onclick="closeLocationModal()" style="background: #f5f5f5; border: none; font-size: 18px; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">✕</button>
      </div>

      <div style="background: #fef9f9; border-left: 4px solid #b71c1c; border-radius: 12px; padding: 18px; margin-bottom: 20px;">
        <div style="font-size: 11px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Assigned Donation Date</div>
        <div id="modalDate" style="font-size: 16px; font-weight: 800; color: #2e7d32;"></div>
      </div>

      <div style="margin-bottom: 24px;">
        <div style="font-size: 11px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Location Info &amp; Venue</div>
        <div style="display: flex; gap: 12px; align-items: flex-start; margin-bottom: 12px;">
          <span style="font-size: 22px;">🏢</span>
          <div>
            <div style="font-size: 15px; font-weight: 800; color: #333;" id="modalLocationName">LifeLine Blood Bank</div>
            <div style="font-size: 13.5px; color: #555; margin-top: 3px;" id="modalLocationAddress">123 Main Road, Hubli, Karnataka — 580001</div>
          </div>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start; margin-bottom: 12px;">
          <span style="font-size: 22px;">📞</span>
          <div>
            <div style="font-size: 13.5px; color: #555;">+91-9876543210</div>
            <div style="font-size: 11.5px; color: #888; font-style: italic;">Contact for directions or rescheduling</div>
          </div>
        </div>
        <div style="display: flex; gap: 12px; align-items: flex-start;">
          <span style="font-size: 22px;">🕒</span>
          <div>
            <div style="font-size: 13.5px; color: #555;">09:00 AM - 06:00 PM</div>
            <div style="font-size: 11.5px; color: #888; font-style: italic;">Regular donation hours</div>
          </div>
        </div>
      </div>

      <button onclick="closeLocationModal()" style="width: 100%; padding: 14px; background: linear-gradient(135deg, #e53935, #b71c1c); color: #fff; font-weight: 700; font-size: 15px; border: none; border-radius: 10px; cursor: pointer; box-shadow: 0 4px 15px rgba(229,57,53,0.3); transition: all 0.3s;">
        Got it, Thank you!
      </button>

    </div>
  </div>

  <!-- ADMIN ISSUE CERTIFICATE MODAL -->
  <div class="eligibility-overlay" id="issueOverlay" style="position: fixed; inset: 0; background: rgba(0,0,0,0.65); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 4000; opacity: 0; visibility: hidden; transition: all 0.3s ease;">
    <div class="eligibility-modal" style="background: #fff; border-radius: 20px; padding: 36px; max-width: 500px; width: 90%; box-shadow: 0 20px 50px rgba(0,0,0,0.3); text-align: left; transform: scale(0.9); transition: all 0.3s ease; position: relative;">
      
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 2px solid #feeef0; padding-bottom: 12px;">
        <h2 style="font-size: 20px; font-weight: 800; color: #1565c0; margin: 0; display: flex; align-items: center; gap: 10px;">
          <span>🏆</span> Issue Donation Certificate
        </h2>
        <button onclick="closeIssueModal()" style="background: #f5f5f5; border: none; font-size: 18px; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">✕</button>
      </div>

      <div style="margin-bottom: 20px;">
        <p style="font-size: 14.5px; color: #555; line-height: 1.5; margin: 0 0 16px 0;">
          Verify that <strong id="issueDonorName" style="color: #1565c0;">Rahul Sharma</strong> has successfully donated blood at <strong>LifeLine Blood Bank</strong>, and enter details below to send their certificate.
        </p>

        <form id="issueCertForm" onsubmit="submitCertificate(event)">
          <input type="hidden" id="issueRefDonorId" name="ref_donor_id">
          <input type="hidden" id="issueAdminNotifId" name="admin_notif_id">

          <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 12px; font-weight: 700; color: #666; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Quantity Donated (ml) *</label>
            <input type="number" id="issueQuantity" name="quantity" value="350" required min="100" max="600"
              style="width: 100%; padding: 12px 14px; border: 1.5px solid #ddd; border-radius: 8px; font-size: 15px; box-sizing: border-box;">
          </div>

          <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 12px; font-weight: 700; color: #666; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Date of Donation *</label>
            <input type="date" id="issueDate" name="donation_date" value="<?= date('Y-m-d') ?>" required
              style="width: 100%; padding: 12px 14px; border: 1.5px solid #ddd; border-radius: 8px; font-size: 15px; box-sizing: border-box;">
          </div>

          <button type="submit" id="btnSubmitCert" style="width: 100%; padding: 14px; background: linear-gradient(135deg, #1565c0, #0d47a1); color: #fff; font-weight: 700; font-size: 15px; border: none; border-radius: 10px; cursor: pointer; box-shadow: 0 4px 15px rgba(21,101,192,0.3); transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 8px;">
            <span>🚀</span> Confirm &amp; Issue Certificate
          </button>
        </form>
      </div>

    </div>
  </div>

  <!-- DONOR CERTIFICATE VIEWER MODAL -->
  <div class="eligibility-overlay" id="certOverlay" style="position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 5000; opacity: 0; visibility: hidden; transition: all 0.3s ease; overflow-y: auto; padding: 20px 0;">
    <div style="transform: scale(0.9); transition: all 0.3s ease; max-width: 760px; width: 95%; display: flex; flex-direction: column; align-items: center; gap: 20px; outline: none;" id="certModalContainer">
      
      <!-- Close button raw -->
      <div style="width: 100%; display: flex; justify-content: flex-end;">
        <button onclick="closeCertificateModal()" style="background: rgba(255,255,255,0.2); border: 2px solid #fff; font-size: 18px; width: 36px; height: 36px; border-radius: 50%; color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">✕</button>
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
              <p style="margin: 0;"><strong>Certificate ID:</strong> <span id="pdf_cert_id">10293</span></p>
              <p style="margin: 2px 0 0 0;"><strong>Verification Code:</strong> <span id="pdf_cert_code">A8C9</span></p>
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
        <button onclick="downloadCertificatePDF()" style="padding: 14px 28px; background: linear-gradient(135deg, #b71c1c, #e53935); color: #fff; font-weight: 700; border: none; border-radius: 10px; cursor: pointer; box-shadow: 0 4px 15px rgba(183,28,28,0.25); display: flex; align-items: center; gap: 8px;">
          📥 Download Certificate (PDF)
        </button>
        <button onclick="closeCertificateModal()" style="padding: 14px 28px; background: #fff; color: #333; font-weight: 700; border: 1.5px solid #ccc; border-radius: 10px; cursor: pointer;">
          Close Window
        </button>
      </div>

    </div>
  </div>

  <script src="theme.js"></script>
  <script>
    // ── Location Modal ──
    function showLocationModal(date, location) {
      const overlay = document.getElementById('locationOverlay');
      const modal = overlay.querySelector('.eligibility-modal');
      
      document.getElementById('modalDate').textContent = date;
      if (location) {
        const parts = location.split(' (Phone:');
        const mainAddr = parts[0];
        document.getElementById('modalLocationAddress').textContent = mainAddr;
      }
      
      overlay.style.opacity = '1';
      overlay.style.visibility = 'visible';
      modal.style.transform = 'scale(1)';
    }

    function closeLocationModal() {
      const overlay = document.getElementById('locationOverlay');
      const modal = overlay.querySelector('.eligibility-modal');
      overlay.style.opacity = '0';
      overlay.style.visibility = 'hidden';
      modal.style.transform = 'scale(0.9)';
    }

    // ── Admin Issue Modal ──
    function openIssueModal(refDonorId, adminNotifId, donorName) {
      const overlay = document.getElementById('issueOverlay');
      const modal = overlay.querySelector('.eligibility-modal');
      
      document.getElementById('issueRefDonorId').value = refDonorId;
      document.getElementById('issueAdminNotifId').value = adminNotifId;
      document.getElementById('issueDonorName').textContent = donorName;
      
      overlay.style.opacity = '1';
      overlay.style.visibility = 'visible';
      modal.style.transform = 'scale(1)';
    }

    function closeIssueModal() {
      const overlay = document.getElementById('issueOverlay');
      const modal = overlay.querySelector('.eligibility-modal');
      overlay.style.opacity = '0';
      overlay.style.visibility = 'hidden';
      modal.style.transform = 'scale(0.9)';
    }

    function submitCertificate(e) {
      e.preventDefault();
      const btn = document.getElementById('btnSubmitCert');
      btn.disabled = true;
      btn.textContent = "⌛ Issuing Certificate...";

      const form = document.getElementById('issueCertForm');
      const formData = new FormData(form);

      fetch('send_certificate.php', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(data => {
        if (data.status === 'success') {
          alert("🎉 Certificate has been successfully issued and sent to the donor!");
          location.reload();
        } else {
          alert("⚠️ Error: " + data.msg);
          btn.disabled = false;
          btn.innerHTML = "<span>🚀</span> Confirm &amp; Issue Certificate";
        }
      })
      .catch(() => {
        alert("⚠️ Failed to reach server. Please try again.");
        btn.disabled = false;
        btn.innerHTML = "<span>🚀</span> Confirm &amp; Issue Certificate";
      });
    }

    // ── Donor Certificate Viewer ──
    let activeCertName = "";
    function showCertificateModal(name, age, bloodGroup, amount, date, certId, certCode) {
      const overlay = document.getElementById('certOverlay');
      const container = document.getElementById('certModalContainer');
      
      activeCertName = name.replace(/\s+/g, '_') + '_Donation_Certificate';
      
      document.getElementById('pdf_donor_name').textContent = name;
      document.getElementById('pdf_donor_age').textContent = age;
      document.getElementById('pdf_donor_amount').textContent = amount + " ml";
      document.getElementById('pdf_donor_group').textContent = bloodGroup;
      document.getElementById('pdf_donation_date').textContent = date;
      document.getElementById('pdf_cert_id').textContent = certId;
      document.getElementById('pdf_cert_code').textContent = certCode;

      overlay.style.opacity = '1';
      overlay.style.visibility = 'visible';
      container.style.transform = 'scale(1)';
    }

    function closeCertificateModal() {
      const overlay = document.getElementById('certOverlay');
      const container = document.getElementById('certModalContainer');
      overlay.style.opacity = '0';
      overlay.style.visibility = 'hidden';
      container.style.transform = 'scale(0.9)';
    }

    function downloadCertificatePDF() {
      const element = document.getElementById('pdf-certificate');
      const opt = {
        margin:       10,
        filename:     activeCertName + '.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true, letterRendering: true },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
      };
      
      html2pdf().set(opt).from(element).save();
    }

    // Close overlays when clicking outside
    document.querySelectorAll('.eligibility-overlay').forEach(overlay => {
      overlay.addEventListener('click', function(e) {
        if (e.target === this) {
          if (this.id === 'locationOverlay') closeLocationModal();
          if (this.id === 'issueOverlay') closeIssueModal();
          if (this.id === 'certOverlay') closeCertificateModal();
        }
      });
    });
  </script>
</body>
</html>
