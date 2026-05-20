<?php
session_start();

// ✅ Check login and role
if (!isset($_SESSION['donor_id'])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['role'] !== 'admin') {
    echo "<p style='color:red;text-align:center;'>Access denied. Admins only.</p>";
    exit();
}

$conn = new mysqli("localhost", "root", "", "BDMS", 3307);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Use prepared statement for safety
if (isset($_POST['blood_group'], $_POST['quantity'])) {
    $blood_group = $_POST['blood_group'];
    $quantity    = (int)$_POST['quantity'];

    $stmt = $conn->prepare("UPDATE Stock SET quantity = quantity + ? WHERE blood_group = ?");
    $stmt->bind_param("is", $quantity, $blood_group);
    $stmt->execute();
    $stmt->close();

    echo "<p style='color:green; font-size:22px;'>Stock updated successfully for $blood_group (+$quantity ml)</p>";
}

// ✅ Always display the current stock table
$sql = "SELECT blood_group, quantity FROM Stock ORDER BY blood_group";
$result = $conn->query($sql);

echo "<h2>Current Blood Stock</h2>";
echo "<table border='1' cellpadding='10' style='border-collapse:collapse;'>
        <tr><th>Blood Group</th><th>Quantity (ml)</th></tr>";

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row['blood_group']."</td><td>".$row['quantity']."</td></tr>";
    }
} else {
    echo "<tr><td colspan='2'>No stock available</td></tr>";
}
echo "</table>";

// ✅ Back to Home button
echo "<br><form action='index.php' method='get'>
        <button type='submit' style='margin-top:10px;padding:8px 15px;'>Go Back to Home</button>
      </form>";

$conn->close();
?>
