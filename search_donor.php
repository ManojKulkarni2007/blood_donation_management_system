<?php
include_once 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Search Donors - Blood Donation Management System</title>
  <link rel="stylesheet" href="style.css?v=6">
  <style>
    .search-wrap {
      max-width: 960px;
      margin: 0 auto;
    }
    /* Search form box */
    .search-box {
      background: #fff;
      border-radius: 16px;
      padding: 30px 36px;
      box-shadow: 0 6px 24px rgba(183,28,28,0.1);
      margin-bottom: 36px;
      text-align: center;
    }
    .search-box p {
      font-size: 14.5px;
      color: #777;
      margin-bottom: 24px;
    }
    .search-fields {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      max-width: 580px;
      margin: 0 auto 22px;
    }
    .sfield label {
      display: block;
      font-weight: 700;
      color: #b71c1c;
      font-size: 13px;
      margin-bottom: 7px;
      text-align: left;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .sfield select,
    .sfield input {
      width: 100%;
      padding: 11px 14px;
      border: 1.5px solid #e0e0e0;
      border-radius: 8px;
      font-size: 14.5px;
      font-family: inherit;
      background: #fafafa;
      transition: border-color 0.2s, box-shadow 0.2s;
      margin-bottom: 0;
      box-sizing: border-box;
    }
    .sfield select:focus,
    .sfield input:focus {
      outline: none;
      border-color: #e53935;
      box-shadow: 0 0 0 3px rgba(229,57,53,0.1);
      background: #fff;
    }
    .search-btn {
      padding: 13px 52px;
      background: linear-gradient(135deg, #e53935, #b71c1c);
      border: none;
      color: #fff;
      font-size: 15px;
      font-weight: 700;
      border-radius: 10px;
      cursor: pointer;
      box-shadow: 0 4px 16px rgba(229,57,53,0.35);
      transition: transform 0.2s, box-shadow 0.2s;
      display: inline-block;
      margin: 0;
    }
    .search-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(229,57,53,0.45);
    }

    /* Donor results */
    .results-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
    }
    .donor-card {
      background: #fff;
      border-radius: 14px;
      border-left: 5px solid #e53935;
      padding: 22px 22px 22px 22px;
      box-shadow: 0 4px 16px rgba(183,28,28,0.08);
      position: relative;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .donor-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 24px rgba(183,28,28,0.15);
    }
    .donor-card h3 {
      margin: 0 60px 12px 0;
      color: #b71c1c;
      font-size: 18px;
      font-weight: 700;
    }
    .donor-card p {
      margin: 5px 0;
      font-size: 13.5px;
      color: #555;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .donor-card p strong { color: #333; min-width: 70px; }
    .blood-badge {
      position: absolute;
      top: 18px; right: 18px;
      background: linear-gradient(135deg, #e53935, #b71c1c);
      color: #fff;
      padding: 6px 14px;
      font-weight: 800;
      border-radius: 20px;
      font-size: 14px;
      box-shadow: 0 3px 10px rgba(229,57,53,0.35);
    }
    .no-results {
      text-align: center;
      padding: 52px 20px;
      font-size: 16px;
      color: #aaa;
      grid-column: 1 / -1;
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.05);
    }
    .no-results .nr-icon { font-size: 48px; margin-bottom: 14px; }

    /* Modal & Buttons */
    .view-details-btn {
      margin-top: 15px;
      width: 100%;
      padding: 10px;
      background: rgba(229, 57, 53, 0.1);
      border: 1px solid rgba(229, 57, 53, 0.2);
      color: #b71c1c;
      font-weight: 700;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.2s;
    }
    .view-details-btn:hover {
      background: #e53935;
      color: #fff;
    }
    body.dark-theme .view-details-btn { color: #ffcdd2; border-color: rgba(255,255,255,0.15); background: rgba(255,255,255,0.05); }
    body.dark-theme .view-details-btn:hover { background: #e53935; color: #fff; }

    .modal-overlay {
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
    .modal-overlay.active { opacity: 1; visibility: visible; }
    .modal-content {
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
    .modal-overlay.active .modal-content { transform: translateY(0) scale(1); }
    body.dark-theme .modal-content { background: var(--bg-card); border: 1px solid var(--border-color); }
    .close-modal {
      position: absolute; top: 15px; right: 20px; font-size: 28px; cursor: pointer; color: #888;
    }
    .close-modal:hover { color: #e53935; }
    #modalName { font-size: 24px; color: #b71c1c; margin-bottom: 5px; }
    body.dark-theme #modalName { color: var(--text-main); }
    .modal-badge {
      display: inline-block; background: #e53935; color: #fff; padding: 4px 12px; border-radius: 20px; font-weight: 800; font-size: 14px; margin-bottom: 15px;
    }
    .modal-loc { font-size: 15px; color: #666; margin-bottom: 25px; line-height: 1.4; }
    body.dark-theme .modal-loc { color: #bbb; }
    .modal-contact-box {
      background: rgba(229, 57, 53, 0.05); border: 1px dashed rgba(229, 57, 53, 0.3); padding: 20px; border-radius: 12px;
    }
    body.dark-theme .modal-contact-box { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1); }
    .modal-contact-box p { font-size: 13.5px; color: #555; margin-bottom: 10px; }
    body.dark-theme .modal-contact-box p { color: #aaa; }
    #modalContact { font-size: 22px; color: #333; font-weight: 800; margin-bottom: 15px; letter-spacing: 1px; }
    body.dark-theme #modalContact { color: #fff; }
    .call-btn {
      display: block; width: 100%; padding: 12px; background: #2e7d32; color: #fff; text-decoration: none; font-weight: 700; border-radius: 8px; transition: background 0.2s;
    }
    .call-btn:hover { background: #1b5e20; }

    @media (max-width: 700px) {
      .search-fields { grid-template-columns: 1fr; }
      .results-grid  { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <div class="page-header">
      <a href="index.php" class="back-link">← Back to Home</a>
      <h1>🔍 Search Blood Donors</h1>
      <p>Find available voluntary donors by blood group and city.</p>
    </div>

    <div class="page-body">
      <div class="search-wrap">

        <!-- Search Form -->
        <div class="search-box">
          <p>Enter the desired blood group and/or location to find active voluntary donors.</p>
          <form method="GET" action="search_donor.php">
            <div class="search-fields">
              <div class="sfield">
                <label for="blood_group">Blood Group</label>
                <select id="blood_group" name="blood_group">
                  <option value="">All Groups</option>
                  <option value="A+">A+</option><option value="A-">A-</option>
                  <option value="B+">B+</option><option value="B-">B-</option>
                  <option value="O+">O+</option><option value="O-">O-</option>
                  <option value="AB+">AB+</option><option value="AB-">AB-</option>
                </select>
              </div>
              <div class="sfield">
                <label for="location">City / Location</label>
                <input type="text" id="location" name="location"
                  placeholder="e.g. Hubli"
                  value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>">
              </div>
            </div>
            <button type="submit" class="search-btn">🔍 Search Donors</button>
          </form>
        </div>

        <!-- Results -->
        <div class="section-title">🩸 Matching Donors</div>
        <div class="results-grid">
          <?php
            $conn = new mysqli("localhost", "root", "", "BDMS", 3307);
            if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

            $bg  = isset($_GET['blood_group']) ? $_GET['blood_group'] : '';
            $loc = isset($_GET['location'])    ? $_GET['location']    : '';

            if (!empty($bg)) {
              echo "<script>document.getElementById('blood_group').value = '" . htmlspecialchars($bg) . "';</script>";
            }

            $sql = "SELECT * FROM Donar WHERE 1=1";
            if (!empty($bg))  $sql .= " AND donar_blood_group = '"  . $conn->real_escape_string($bg)  . "'";
            if (!empty($loc)) $sql .= " AND donar_address LIKE '%" . $conn->real_escape_string($loc) . "%'";

            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                echo "<div class='donor-card'>
                        <h3>" . htmlspecialchars($row['donar_name']) . "</h3>
                        <span class='blood-badge'>" . htmlspecialchars($row['donar_blood_group']) . "</span>
                        <p><strong>Age:</strong> " . htmlspecialchars($row['donar_age']) . " yrs</p>
                        <p><strong>Gender:</strong> " . htmlspecialchars($row['donar_gender']) . "</p>
                        <button class='view-details-btn' 
                                data-name='" . htmlspecialchars($row['donar_name']) . "'
                                data-bg='" . htmlspecialchars($row['donar_blood_group']) . "'
                                data-loc='" . htmlspecialchars($row['donar_address']) . "'
                                data-contact='" . htmlspecialchars($row['donar_contact']) . "'>
                            View Details
                        </button>
                      </div>";
              }
            } else {
              echo "<div class='no-results'>
                      <div class='nr-icon'>🔍</div>
                      No matching donors found.<br>Please try adjusting your filters.
                    </div>";
            }
            $conn->close();
          ?>
        </div>

      </div>
    </div>

    <footer>
      <p>📞 +91-9876543210 &nbsp;|&nbsp; ✉️ <a href="mailto:support@bdms.com">support@bdms.com</a></p>
      <p>© 2026 Blood Donation Management System. All Rights Reserved.</p>
    </footer>
  </div>

  <!-- Donor Details Modal -->
  <div id="donorModal" class="modal-overlay">
    <div class="modal-content">
      <span class="close-modal">&times;</span>
      <h2 id="modalName">Donor Name</h2>
      <div class="modal-badge" id="modalBg">A+</div>
      <p class="modal-loc">📍 <span id="modalLoc">Location</span></p>
      
      <div class="modal-contact-box">
        <p>To approach them, please call this number:</p>
        <h3 id="modalContact">+91-0000000000</h3>
        <a href="#" id="modalCallBtn" class="call-btn">📞 Call Now</a>
      </div>
    </div>
  </div>

  <script>
    // Modal Interaction Script
    const modal = document.getElementById('donorModal');
    const closeBtn = document.querySelector('.close-modal');
    
    document.querySelectorAll('.view-details-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        // Populate modal data from button attributes
        document.getElementById('modalName').textContent = btn.getAttribute('data-name');
        document.getElementById('modalBg').textContent = btn.getAttribute('data-bg');
        document.getElementById('modalLoc').textContent = btn.getAttribute('data-loc');
        
        const contact = btn.getAttribute('data-contact');
        document.getElementById('modalContact').textContent = contact;
        document.getElementById('modalCallBtn').href = 'tel:' + contact;
        
        // Show modal
        modal.classList.add('active');
      });
    });
    
    // Close modal handlers
    closeBtn.addEventListener('click', () => modal.classList.remove('active'));
    window.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.classList.remove('active');
      }
    });
  </script>
</body>
</html>
