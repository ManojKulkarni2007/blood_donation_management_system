<?php
include_once 'includes/auth.php';
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

      // Filter Logic
      $filter_type = isset($_GET['filter_type']) ? $_GET['filter_type'] : 'name';
      $filter_value = isset($_GET['filter_value']) ? $conn->real_escape_string($_GET['filter_value']) : '';

      $col_where = [];
      $iss_where = [];

      if ($is_donor) {
          $col_where[] = "d.donar_id = $current_donor_id";
      }

      if (!empty($filter_value)) {
          if ($filter_type === 'name') {
              $col_where[] = "d.donar_name LIKE '%$filter_value%'";
              $iss_where[] = "r.recipient_name LIKE '%$filter_value%'";
          } elseif ($filter_type === 'id') {
              if (preg_match('/^(DON|REC)[A-Z]{3}(\d{4,})$/i', $filter_value, $matches)) {
                  $num_id = (int)$matches[2];
                  if (strtoupper($matches[1]) === 'DON') {
                      $col_where[] = "c.collection_id = $num_id";
                      $iss_where[] = "1 = 0";
                  } else {
                      $iss_where[] = "i.issue_id = $num_id";
                      $col_where[] = "1 = 0";
                  }
              } else {
                  $col_where[] = "d.donar_id = '$filter_value'";
                  $iss_where[] = "r.recipient_id = '$filter_value'";
              }
          } elseif ($filter_type === 'date') {
              $col_where[] = "DATE(c.collection_date) = '$filter_value'";
              $iss_where[] = "DATE(i.issue_date) = '$filter_value'";
          } elseif ($filter_type === 'blood_group') {
              $col_where[] = "d.donar_blood_group LIKE '%$filter_value%'";
              $iss_where[] = "r.recipient_blood_group LIKE '%$filter_value%'";
          } elseif ($filter_type === 'location') {
              $col_where[] = "d.donar_address LIKE '%$filter_value%'";
              $iss_where[] = "r.recipient_hospital LIKE '%$filter_value%'";
          }
      }

      $col_where_clause = count($col_where) > 0 ? "WHERE " . implode(" AND ", $col_where) : "";
      $iss_where_clause = count($iss_where) > 0 ? "WHERE " . implode(" AND ", $iss_where) : "";
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
      
      <!-- Filter Section -->
      <div class="filter-section" style="background: var(--bg-card); padding: 20px 25px; border-radius: 14px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid var(--border-color);">
        <form method="GET" action="transactions.php" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
          <div style="flex: 1; min-width: 150px;">
            <label for="filter_type" style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Filter By</label>
            <select id="filter_type" name="filter_type" style="width: 100%; padding: 12px 15px; border-radius: 10px; border: 1.5px solid var(--border-color); background: rgba(0,0,0,0.02); color: var(--text-main); font-family: inherit;">
              <option value="name" <?php echo ($filter_type === 'name') ? 'selected' : ''; ?>>Name</option>
              <option value="id" <?php echo ($filter_type === 'id') ? 'selected' : ''; ?>>ID (Transaction / Donor / Recipient)</option>
              <option value="date" <?php echo ($filter_type === 'date') ? 'selected' : ''; ?>>Transaction Date</option>
              <option value="blood_group" <?php echo ($filter_type === 'blood_group') ? 'selected' : ''; ?>>Blood Group</option>
              <option value="location" <?php echo ($filter_type === 'location') ? 'selected' : ''; ?>>Location</option>
            </select>
          </div>
          <div style="flex: 2; min-width: 200px;">
            <label for="filter_value" style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Search Value</label>
            <input type="text" id="filter_value" name="filter_value" value="<?php echo htmlspecialchars($_GET['filter_value'] ?? ''); ?>" placeholder="Enter search value..." style="width: 100%; padding: 12px 15px; border-radius: 10px; border: 1.5px solid var(--border-color); background: rgba(0,0,0,0.02); color: var(--text-main); font-family: inherit;" required>
          </div>
          <div style="display: flex; gap: 12px;">
            <button type="submit" style="padding: 12px 24px; border-radius: 10px; background: linear-gradient(135deg, #e53935, #b71c1c); color: #fff; border: none; font-weight: 700; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 4px 12px rgba(229,57,53,0.3);">Filter</button>
            <a href="transactions.php" style="padding: 12px 24px; border-radius: 10px; background: transparent; color: var(--text-main); text-decoration: none; font-weight: 700; border: 1.5px solid var(--border-color); display: flex; align-items: center; transition: background 0.2s;">Clear</a>
          </div>
        </form>
      </div>

      <script>
        document.getElementById('filter_type').addEventListener('change', function() {
            var val = this.value;
            var input = document.getElementById('filter_value');
            input.value = ''; // Clear previous value when type changes
            if (val === 'date') {
                input.type = 'date';
                input.placeholder = '';
            } else {
                input.type = 'text';
                if (val === 'blood_group') input.placeholder = 'e.g. A+, O-';
                else if (val === 'id') input.placeholder = 'Enter ID number...';
                else if (val === 'location') input.placeholder = 'e.g. Hubli, Bangalore...';
                else input.placeholder = 'Enter Name...';
            }
        });
        
        // Trigger on load
        if(document.getElementById('filter_type').value === 'date') {
            document.getElementById('filter_value').type = 'date';
        }
      </script>

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
                <th>Location</th>
                <th>Collection ID</th>
                <th>Quantity Collected</th>
                <th>Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $sql1 = "SELECT d.donar_id, d.donar_name, d.donar_address, c.collection_id, c.collection_quantity, c.collection_date
                         FROM Donar d
                         JOIN Collection c ON d.donar_id = c.donar_id
                         $col_where_clause
                         ORDER BY c.collection_date DESC";
                
                $result1 = $conn->query($sql1);
                if ($result1 && $result1->num_rows > 0) {
                    $i = 1;
                    while ($row = $result1->fetch_assoc()) {
                        $name_part = strtoupper(substr(str_pad(preg_replace('/[^a-zA-Z]/', '', $row['donar_name']), 3, 'X'), 0, 3));
                        $id_part = str_pad($row['collection_id'], 4, '0', STR_PAD_LEFT);
                        $display_id = "DON" . $name_part . $id_part;
                        
                        echo "<tr>
                                <td>" . $i++ . "</td>
                                <td><strong>" . htmlspecialchars($row['donar_name']) . "</strong></td>
                                <td>" . htmlspecialchars($row['donar_address']) . "</td>
                                <td>#" . htmlspecialchars($display_id) . "</td>
                                <td>" . htmlspecialchars($row['collection_quantity']) . " ml</td>
                                <td>" . htmlspecialchars($row['collection_date']) . "</td>
                                <td><span class='badge badge-green'>✓ Collected</span></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'><div class='no-data'><div class='icon'>🩸</div>No donation records found.</div></td></tr>";
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
                  <th>Location</th>
                  <th>Issue ID</th>
                  <th>Quantity Issued</th>
                  <th>Date</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $sql2 = "SELECT r.recipient_id, r.recipient_name, r.recipient_hospital, i.issue_id, i.quantity, i.issue_date
                           FROM Recipient r
                           JOIN Issue i ON r.recipient_id = i.recipient_id
                           $iss_where_clause
                           ORDER BY i.issue_date DESC";
                  $result2 = $conn->query($sql2);
                  if ($result2 && $result2->num_rows > 0) {
                      $j = 1;
                      while ($row = $result2->fetch_assoc()) {
                          $name_part = strtoupper(substr(str_pad(preg_replace('/[^a-zA-Z]/', '', $row['recipient_name']), 3, 'X'), 0, 3));
                          $id_part = str_pad($row['issue_id'], 4, '0', STR_PAD_LEFT);
                          $display_id = "REC" . $name_part . $id_part;

                          echo "<tr>
                                  <td>" . $j++ . "</td>
                                  <td><strong>" . htmlspecialchars($row['recipient_name']) . "</strong></td>
                                  <td>" . htmlspecialchars($row['recipient_hospital']) . "</td>
                                  <td>#" . htmlspecialchars($display_id) . "</td>
                                  <td>" . htmlspecialchars($row['quantity']) . " ml</td>
                                  <td>" . htmlspecialchars($row['issue_date']) . "</td>
                                  <td><span class='badge badge-blue'>✓ Issued</span></td>
                                </tr>";
                      }
                  } else {
                      echo "<tr><td colspan='7'><div class='no-data'><div class='icon'>🏥</div>No recipient issue records found.</div></td></tr>";
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
