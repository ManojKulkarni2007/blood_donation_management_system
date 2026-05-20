<?php
session_start();
if (!isset($_SESSION['donor_id'])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['role'] !== 'admin') {
    echo "<p style='color:red;text-align:center;'>Access denied. Admins only.</p>";
    exit();
}

include("includes/navbar.php");
include("includes/header.php");

$conn = new mysqli("localhost", "root", "", "BDMS", 3307);

// Donor Transactions
echo "<h2>Donor Transactions</h2>";
$sql1 = "SELECT d.donar_id, d.donar_name, c.collection_id, c.collection_quantity, c.collection_date
         FROM Donar d
         JOIN Collection c ON d.donar_id = c.donar_id";
$result1 = $conn->query($sql1);

echo "<table border='1' cellpadding='10'>
        <tr><th>Donor ID</th><th>Name</th><th>Collection ID</th><th>Quantity (ml)</th><th>Date</th></tr>";
while($row = $result1->fetch_assoc()) {
    echo "<tr><td>".$row['donar_id']."</td><td>".$row['donar_name']."</td>
              <td>".$row['collection_id']."</td><td>".$row['collection_quantity']."</td>
              <td>".$row['collection_date']."</td></tr>";
}
echo "</table>";

// Recipient Transactions
echo "<h2>Recipient Transactions</h2>";
$sql2 = "SELECT r.recipient_id, r.recipient_name, i.issue_id, i.quantity, i.issue_date
         FROM Recipient r
         JOIN Issue i ON r.recipient_id = i.recipient_id";
$result2 = $conn->query($sql2);

echo "<table border='1' cellpadding='10'>
        <tr><th>Recipient ID</th><th>Name</th><th>Issue ID</th><th>Quantity (ml)</th><th>Date</th></tr>";
while($row = $result2->fetch_assoc()) {
    echo "<tr><td>".$row['recipient_id']."</td><td>".$row['recipient_name']."</td>
              <td>".$row['issue_id']."</td><td>".$row['quantity']."</td>
              <td>".$row['issue_date']."</td></tr>";
}
echo "</table>";

$conn->close();

include("includes/footer.php");
include("includes/back.php");
?>
