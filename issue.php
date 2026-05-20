<?php
// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "bdms";

$conn = new mysqli($servername, $username, $password, $dbname, 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form inputs safely
$recipient_id   = $_POST['recipient_id'] ?? '';
$blood_group    = $_POST['blood_group'] ?? '';
$issue_quantity = $_POST['quantity'] ?? 0;
$issue_date     = $_POST['issue_date'] ?? '';

if (empty($recipient_id) || empty($blood_group) || empty($issue_quantity) || empty($issue_date)) {
    die("<p style='color:red;'>Error: Missing required form fields.</p>");
}

// ✅ Check stock availability first
$stmt_check = $conn->prepare("SELECT quantity FROM Stock WHERE blood_group = ?");
$stmt_check->bind_param("s", $blood_group);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows === 0) {
    die("<p style='color:red;'>Error: Blood group $blood_group not found in stock.</p>");
}

$row = $result->fetch_assoc();
if ($row['quantity'] < $issue_quantity) {
    die("<p style='color:red;'>Error: Not enough stock available. Current stock: {$row['quantity']} ml</p>");
}
$stmt_check->close();

// ✅ Insert into Issue table using prepared statement
$stmt_issue = $conn->prepare("INSERT INTO Issue (recipient_id, blood_group, quantity, issue_date) VALUES (?, ?, ?, ?)");
$stmt_issue->bind_param("ssis", $recipient_id, $blood_group, $issue_quantity, $issue_date);

if ($stmt_issue->execute()) {
    // ✅ Update stock safely
    $stmt_stock = $conn->prepare("UPDATE Stock SET quantity = quantity - ? WHERE blood_group = ?");
    $stmt_stock->bind_param("is", $issue_quantity, $blood_group);

    if ($stmt_stock->execute()) {
        echo "<h2 style='color:green;'>Blood issue recorded successfully!</h2>";

        // Confirmation table
        echo "<div style='text-align:center;'>
                <table border='1' cellpadding='8' style='margin-top:20px; border-collapse:collapse;'>
                  <tr><th>Recipient ID</th><th>Blood Group</th><th>Quantity</th><th>Date</th></tr>
                  <tr><td>$recipient_id</td><td>$blood_group</td><td>$issue_quantity</td><td>$issue_date</td></tr>
                </table>
              </div>";
    } else {
        echo "<p style='color:red;'>Error updating stock: " . $conn->error . "</p>";
    }
    $stmt_stock->close();
} else {
    echo "<p style='color:red;'>Error recording issue: " . $conn->error . "</p>";
}
$stmt_issue->close();

$conn->close();
?>
