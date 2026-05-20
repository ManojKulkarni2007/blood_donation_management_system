<?php
session_start();
if (!isset($_SESSION['donor_id'])) {
    header("Location: login.php");
    exit();
}

include("includes/navbar.php");
include("includes/header.php");

// Collect and sanitize inputs
$donor     = strtoupper(trim($_POST['donor_group'] ?? ''));
$recipient = strtoupper(trim($_POST['recipient_group'] ?? ''));

// Compatibility rules (Recipient → Compatible Donors)
$compatibility = [
    "O-"  => ["O-"],
    "O+"  => ["O+", "O-"],
    "A-"  => ["A-", "O-"],
    "A+"  => ["A+", "A-", "O+", "O-"],
    "B-"  => ["B-", "O-"],
    "B+"  => ["B+", "B-", "O+", "O-"],
    "AB-" => ["AB-", "A-", "B-", "O-"],
    "AB+" => ["O-", "O+", "A-", "A+", "B-", "B+", "AB-", "AB+"]
];

echo "<h2 style='text-align:center;'>Compatibility Result</h2>";

// ✅ Validate recipient blood group
if (!isset($compatibility[$recipient])) {
    echo "<p style='color:red;text-align:center;'>Error: Invalid recipient blood group entered.</p>";
} else {
    if (in_array($donor, $compatibility[$recipient])) {
        echo "<p style='color:green;text-align:center;'>✅ Match Found: Donor $donor can donate to Recipient $recipient</p>";
        echo "<p style='text-align:center;'><a href='issue.php' style='color:blue; font-weight:bold;'>Go to Issue Page</a></p>";
    } else {
        echo "<p style='color:red;text-align:center;'>❌ Not Compatible: Donor $donor cannot donate to Recipient $recipient</p>";
    }
}

// ✅ Compatibility table
echo "<h2 style='text-align:center;'>Blood Group Compatibility Table</h2>";
echo "<div style='display:flex; justify-content:center;'>
        <table border='1' cellpadding='10' style='border-collapse:collapse;'>
          <tr><th>Recipient Blood Group</th><th>Compatible Donor Groups</th></tr>
          <tr><td>O-</td><td>O-</td></tr>
          <tr><td>O+</td><td>O+, O-</td></tr>
          <tr><td>A-</td><td>A-, O-</td></tr>
          <tr><td>A+</td><td>A+, A-, O+, O-</td></tr>
          <tr><td>B-</td><td>B-, O-</td></tr>
          <tr><td>B+</td><td>B+, B-, O+, O-</td></tr>
          <tr><td>AB-</td><td>AB-, A-, B-, O-</td></tr>
          <tr><td>AB+</td><td>All groups (Universal Recipient)</td></tr>
        </table>
      </div>";

echo "<div style='text-align:center; margin-top:20px;'>
        <a href='index.php' style='color:darkred; font-weight:bold;'>Back to Dashboard</a>
      </div>";

include("includes/footer.php");
include("includes/back.php");
?>
