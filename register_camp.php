<?php
include_once 'includes/auth.php';

// Verify the user is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'donor') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'msg' => 'Unauthorized access.']);
    exit();
}

$conn = new mysqli("localhost", "root", "", "BDMS", 3307);
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'msg' => 'Connection failed.']);
    exit();
}

$donor_id = intval($_SESSION['donor_id']);

// Ensure table CampRegistrations exists
$conn->query("CREATE TABLE IF NOT EXISTS CampRegistrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT,
    donor_name VARCHAR(150),
    camp_location VARCHAR(255),
    camp_date VARCHAR(100),
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Collect post data
$camp_location = $conn->real_escape_string(trim($_POST['camp_location'] ?? ''));
$camp_date     = $conn->real_escape_string(trim($_POST['camp_date'] ?? ''));

if (empty($camp_location) || empty($camp_date)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'msg' => 'Invalid camp details.']);
    $conn->close();
    exit();
}

// Fetch donor info
$res_donor = $conn->query("SELECT donar_name, donar_blood_group, donar_contact FROM Donar WHERE donar_id = $donor_id LIMIT 1");
if ($res_donor && $donor = $res_donor->fetch_assoc()) {
    $donor_name  = $donor['donar_name'];
    $blood_group = $donor['donar_blood_group'];
    $contact     = $donor['donar_contact'];
} else {
    $donor_name  = $_SESSION['donor_name'];
    $blood_group = 'Unknown';
    $contact     = 'N/A';
}

// Check if already registered
$check = $conn->query("SELECT id FROM CampRegistrations WHERE donor_id = $donor_id AND camp_location = '$camp_location' LIMIT 1");
if ($check && $check->num_rows > 0) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'msg' => 'You are already registered for this camp.']);
    $conn->close();
    exit();
}

// Insert registration
$esc_dname = $conn->real_escape_string($donor_name);
$sql_insert = "INSERT INTO CampRegistrations (donor_id, donor_name, camp_location, camp_date) 
               VALUES ($donor_id, '$esc_dname', '$camp_location', '$camp_date')";
$success = $conn->query($sql_insert);

if ($success) {
    // 1. Send registered message/notification to donor
    $donor_msg = "🎟️ You successfully registered to participate in the upcoming donation camp at $camp_location on $camp_date! Thank you for taking a role in saving lives.";
    $esc_dmsg = $conn->real_escape_string($donor_msg);
    $conn->query("INSERT INTO Notifications (donor_id, message) VALUES ($donor_id, '$esc_dmsg')");

    // 2. Send notification to admin
    $admin_msg = "ℹ️ Donor $donor_name (Blood Group: $blood_group, Contact: $contact) has registered for the donation camp at $camp_location on $camp_date.";
    $esc_amsg = $conn->real_escape_string($admin_msg);
    $conn->query("INSERT INTO Notifications (donor_id, message) VALUES (9999, '$esc_amsg')");

    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'msg' => 'Successfully registered!']);
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'msg' => 'Failed to save registration.']);
}

$conn->close();
?>
