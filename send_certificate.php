<?php
include_once 'includes/auth.php';

// Verify the user is indeed an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'msg' => 'Unauthorized access.']);
    exit();
}

// Check database connection
$conn = new mysqli("localhost", "root", "", "BDMS", 3307);
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'msg' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

// Collect POST fields
$ref_donor_id   = intval($_POST['ref_donor_id'] ?? 0);
$admin_notif_id = intval($_POST['admin_notif_id'] ?? 0);
$donated_amount = intval($_POST['quantity'] ?? 350);
$donation_date  = $conn->real_escape_string(trim($_POST['donation_date'] ?? date('Y-m-d')));

if ($ref_donor_id <= 0 || $admin_notif_id <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'msg' => 'Invalid parameters.']);
    $conn->close();
    exit();
}

// Fetch donor statistics to construct the certificate
$res = $conn->query("SELECT donar_name, donar_age, donar_blood_group FROM Donar WHERE donar_id = $ref_donor_id LIMIT 1");
if ($res && $donor = $res->fetch_assoc()) {
    $donor_name        = $donor['donar_name'];
    $donor_age         = intval($donor['donar_age']);
    $donor_blood_group = $donor['donar_blood_group'];
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'msg' => 'Donor record not found.']);
    $conn->close();
    exit();
}

// Begin transaction to ensure consistent database updates
$conn->begin_transaction();

try {
    // 1. Insert a record into Collection table
    $sql_collection = "INSERT INTO Collection (donar_id, collection_date, collection_quantity) 
                       VALUES ($ref_donor_id, '$donation_date', $donated_amount)";
    $conn->query($sql_collection);

    // 2. Update Blood Stock in Stock table
    $sql_stock = "UPDATE Stock SET quantity = quantity + $donated_amount WHERE blood_group = '$donor_blood_group'";
    $conn->query($sql_stock);

    // 3. Generate randomized Certificate ID and Verification Code
    $rand_id   = rand(10000, 99999);
    $chars     = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $rand_code = '';
    for ($i = 0; $i < 4; $i++) {
        $rand_code .= $chars[rand(0, strlen($chars) - 1)];
    }
    
    $cert_id   = "BDMS-TXN-$rand_id";
    $cert_code = "BDMS-APPROVED-$rand_code";

    // 4. Insert Certificate Notification into Notifications table for the donor
    $esc_name     = $conn->real_escape_string($donor_name);
    $esc_bg       = $conn->real_escape_string($donor_blood_group);
    $donor_msg    = "🏆 LifeLine Blood Bank has issued your Blood Donation Certificate! Click here to view and print your certificate.";
    $esc_donor_msg= $conn->real_escape_string($donor_msg);

    $sql_cert_notif = "INSERT INTO Notifications 
                       (donor_id, message, is_certificate, cert_donor_name, cert_donor_age, 
                        cert_donor_blood_group, cert_donated_amount, cert_donation_date, cert_id, cert_code) 
                       VALUES 
                       ($ref_donor_id, '$esc_donor_msg', 1, '$esc_name', $donor_age, 
                        '$esc_bg', $donated_amount, '$donation_date', '$cert_id', '$cert_code')";
    $conn->query($sql_cert_notif);

    // 5. Mark the admin's registration notification as processed
    $sql_update_admin = "UPDATE Notifications SET is_processed = 1 WHERE id = $admin_notif_id";
    $conn->query($sql_update_admin);

    $conn->commit();
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'msg' => 'Certificate issued and sent successfully!']);
} catch (Exception $e) {
    $conn->rollback();
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'msg' => 'Transaction failed: ' . $e->getMessage()]);
}

$conn->close();
?>
