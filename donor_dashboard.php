<?php
session_start();
if (!isset($_SESSION['donor_id'])) {
    header("Location: login.php");
    exit();
}

include("includes/navbar.php");
include("includes/header.php");

// Database connection
$conn = new mysqli("localhost", "root", "", "BDMS", 3307);
if ($conn->connect_error) {
    die("<p style='color:red;text-align:center;'>Connection failed: " . $conn->connect_error . "</p>");
}

$donor_id = $_SESSION['donor_id'];

// ✅ Fetch donor profile
$stmt = $conn->prepare("SELECT donar_name, donar_blood_group, last_donation_date FROM Donar WHERE donar_id = ?");
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$result = $stmt->get_result();
$donor = $result->fetch_assoc();
$stmt->close();

// Eligibility check (90 days gap)
$eligible = "Yes";
if (!empty($donor['last_donation_date'])) {
    $lastDonation = new DateTime($donor['last_donation_date']);
    $today = new DateTime();
    $interval = $lastDonation->diff($today)->days;
    if ($interval < 90) {
        $eligible = "No (Next eligible after " . $lastDonation->modify("+90 days")->format("Y-m-d") . ")";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Donor Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2 style="text-align:center;">Welcome to Donor Dashboard</h2>

  <!-- Profile Summary -->
  <div style="text-align:center; margin:20px;">
    <h3>Profile Summary</h3>
    <p><strong>Name:</strong> <?php echo $donor['donar_name']; ?></p>
    <p><strong>Blood Group:</strong> <?php echo $donor['donar_blood_group']; ?></p>
    <p><strong>Last Donation:</strong> <?php echo $donor['last_donation_date'] ?? "No donations yet"; ?></p>
    <p><strong>Eligible for Next Donation:</strong> <?php echo $eligible; ?></p>
  </div>

  <!-- Options -->
  <div style="text-align:center; margin:20px;">
    <h3>Options</h3>
    <ul style="list-style:none; padding:0;">
      <li><a href="edit_profile.php">Edit Profile</a></li>
      <li><a href="collection.html">Donate Blood</a></li>
      <li><a href="requests.php">View Requests</a></li>
      <li><a href="donation_history.php">Donation History</a></li>
      <li><a href="notifications.php">Notifications</a></li>
      <li><a href="urgent_requests.php">Urgent Requests Nearby</a></li>
    </ul>
  </div>

  <div style="text-align:center; margin-top:20px;">
    <a href="index.php">Back to Dashboard</a>
  </div>
</body>
</html>

<?php
include("includes/footer.php");
include("includes/back.php");
$conn->close();
?>
