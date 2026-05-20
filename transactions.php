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
  <title>Transaction History - Blood Donation Management System</title>
  <link rel="stylesheet" href="style.css?v=6">
  <style>
    .txn-section { margin-bottom: 48px; }

    .txn-table-wrap {
      overflow-x: auto;
      border-radius: 14px;
      box-shadow: 0 4px 20px rgba(183,28,28,0.09);
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      border-radius: 14px;
      overflow: hidden;
      font-size: 14px;
    }
    body.dark-theme table { background: var(--bg-card); }

    thead tr {
      background: linear-gradient(135deg, #b71c1c, #e53935);
      color: #fff;
    }
    thead th {
      padding: 14px 18px;
      text-align: left;
      font-weight: 700;
      font-size: 13px;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      white-space: nowrap;
    }
    tbody tr {
      border-bottom: 1px solid #f5f5f5;
      transition: background 0.2s;
    }
    body.dark-theme tbody tr { border-bottom-color: var(--border-color); }
    tbody tr:hover { background: #fff8f8; }
    body.dark-theme tbody tr:hover { background: rgba(229,57,53,0.06); }
    tbody tr:last-child { border-bottom: none; }

    tbody td {
      padding: 13px 18px;
      color: #444;
      vertical-align: middle;
    }
    body.dark-theme tbody td { color: #ccc; }

    .badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 700;
    }
    .badge-green { background: rgba(46,125,50,0.1); color: #2e7d32; }
    .badge-blue  { background: rgba(21,101,192,0.1); color: #1565c0; }

    .no-data {
      text-align: center;
      padding: 40px 20px;
      color: #aaa;
      font-size: 15px;
    }
    .no-data .icon { font-size: 40px; margin-bottom: 10px; }
  </style>
</head>
<body>

  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <?php
      $conn = new mysqli("localhost", "root", "", "BDMS", 3307);
      if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

      $current_donor_id = $_SESSION['donor_id'];
      $current_role     = $_SESSION['role'] ?? 'donor';
      $is_donor         = ($current_role === 'donor');
    ?>

    <div class="page-header">
      <a href="index.php" class="back-link">← Back to Home</a>
      <?php if ($is_donor): ?>
        <h1>📋 Your Donation History</h1>
        <p>Thank you, <strong><?php echo htmlspecialchars($_SESSION['donor_name']); ?></strong>! Here is the record of your lifesaving contributions.</p>
      <?php else: ?>
        <h1>📋 Transaction History</h1>
        <p>Complete log of all blood donations collected and issued to recipients.</p>
      <?php endif; ?>
    </div>

    <div class="page-body">
      <!-- Donor Transactions -->
      <div class="txn-section">
        <div class="section-title">
          <?php echo $is_donor ? "🩸 Your Collection Records" : "🩸 Donor Collection Records"; ?>
        </div>
        <div class="txn-table-wrap">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Donor Name</th>
                <th>Collection ID</th>
                <th>Quantity Collected</th>
                <th>Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php
                if ($is_donor) {
                    $sql1 = "SELECT d.donar_id, d.donar_name, c.collection_id, c.collection_quantity, c.collection_date
                             FROM Donar d
                             JOIN Collection c ON d.donar_id = c.donar_id
                             WHERE d.donar_id = $current_donor_id
                             ORDER BY c.collection_date DESC";
                } else {
                    $sql1 = "SELECT d.donar_id, d.donar_name, c.collection_id, c.collection_quantity, c.collection_date
                             FROM Donar d
                             JOIN Collection c ON d.donar_id = c.donar_id
                             ORDER BY c.collection_date DESC";
                }
                $result1 = $conn->query($sql1);
                if ($result1 && $result1->num_rows > 0) {
                    $i = 1;
                    while ($row = $result1->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $i++ . "</td>
                                <td><strong>" . htmlspecialchars($row['donar_name']) . "</strong></td>
                                <td>#" . htmlspecialchars($row['collection_id']) . "</td>
                                <td>" . htmlspecialchars($row['collection_quantity']) . " ml</td>
                                <td>" . htmlspecialchars($row['collection_date']) . "</td>
                                <td><span class='badge badge-green'>✓ Collected</span></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'><div class='no-data'><div class='icon'>🩸</div>No donation records found.</div></td></tr>";
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>

      <?php if (!$is_donor): ?>
        <!-- Recipient Transactions (Only visible to admin/staff) -->
        <div class="txn-section">
          <div class="section-title">🏥 Recipient Issue Records</div>
          <div class="txn-table-wrap">
            <table>
              <thead>
                <tr>
                  <th>#</th>
                  <th>Recipient Name</th>
                  <th>Issue ID</th>
                  <th>Quantity Issued</th>
                  <th>Date</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $sql2 = "SELECT r.recipient_id, r.recipient_name, i.issue_id, i.quantity, i.issue_date
                           FROM Recipient r
                           JOIN Issue i ON r.recipient_id = i.recipient_id
                           ORDER BY i.issue_date DESC";
                  $result2 = $conn->query($sql2);
                  if ($result2 && $result2->num_rows > 0) {
                      $j = 1;
                      while ($row = $result2->fetch_assoc()) {
                          echo "<tr>
                                  <td>" . $j++ . "</td>
                                  <td><strong>" . htmlspecialchars($row['recipient_name']) . "</strong></td>
                                  <td>#" . htmlspecialchars($row['issue_id']) . "</td>
                                  <td>" . htmlspecialchars($row['quantity']) . " ml</td>
                                  <td>" . htmlspecialchars($row['issue_date']) . "</td>
                                  <td><span class='badge badge-blue'>✓ Issued</span></td>
                                </tr>";
                      }
                  } else {
                      echo "<tr><td colspan='6'><div class='no-data'><div class='icon'>🏥</div>No recipient issue records found.</div></td></tr>";
                  }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endif; ?>

      <?php $conn->close(); ?>

    </div><!-- /page-body -->

    <footer>
      <p>📞 +91-9876543210 &nbsp;|&nbsp; ✉️ <a href="mailto:support@bdms.com">support@bdms.com</a></p>
      <p>© 2026 Blood Donation Management System. All Rights Reserved.</p>
    </footer>
  </div>

  <script src="theme.js"></script>
</body>
</html>
