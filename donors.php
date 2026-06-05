<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: donor.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "BDMS", 3307);
if ($conn->connect_error) { 
    if (isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'msg' => 'Connection failed: ' . $conn->connect_error]);
        exit();
    }
    die("Connection failed: " . $conn->connect_error); 
}

session_start();
$is_existing_donor = isset($_SESSION['role']) && $_SESSION['role'] === 'donor';
$existing_donor_id = $_SESSION['donor_id'] ?? 0;

// ── Collect & sanitize all inputs ─────────────────────────────
$name         = $conn->real_escape_string(trim($_POST['donar_name']       ?? ''));
$age          = intval($_POST['donar_age']          ?? 0);
$gender       = $conn->real_escape_string(trim($_POST['donar_gender']     ?? ''));
$blood_group  = $conn->real_escape_string(trim($_POST['donar_blood_group']?? ''));
$contact      = $conn->real_escape_string(trim($_POST['donar_contact']    ?? ''));
$email        = $conn->real_escape_string(trim($_POST['donar_email']      ?? ''));
$address      = $conn->real_escape_string(trim($_POST['donar_address']    ?? ''));
$password     = trim($_POST['reg_password']  ?? '');
$confirm      = trim($_POST['reg_confirm']   ?? '');

// Medical fields
$weight       = floatval($_POST['donar_weight']        ?? 0);
$hemoglobin   = floatval($_POST['donar_hemoglobin']    ?? 0);
$temperature  = floatval($_POST['donar_temperature']   ?? 0);
$bp_systolic  = intval($_POST['donar_bp_systolic']     ?? 0);
$bp_diastolic = intval($_POST['donar_bp_diastolic']    ?? 0);
$last_donation= $conn->real_escape_string(trim($_POST['donar_last_donation'] ?? ''));
$conditions   = $conn->real_escape_string(trim($_POST['donar_medical_conditions'] ?? ''));
$last_donation_val = !empty($last_donation) ? "'$last_donation'" : "NULL";

// Donation amount
$donated_amount = intval($_POST['donated_amount'] ?? 350);

// ── Server-side eligibility validation ───────────────────────
$errors = [];
if ($age < 18 || $age > 65)                      $errors[] = "Age must be between 18 and 65 years.";
if ($weight < 45)                                  $errors[] = "Weight must be at least 45 kg.";
$min_hb = ($gender === 'Female') ? 12.5 : 13.0;
if ($hemoglobin < $min_hb)                         $errors[] = "Hemoglobin is below the required level ($min_hb g/dL for $gender).";
if ($temperature < 97.0 || $temperature > 99.5)   $errors[] = "Body temperature must be between 97.0°F and 99.5°F.";
if ($bp_systolic < 90 || $bp_systolic > 160)       $errors[] = "Systolic blood pressure must be between 90–160 mmHg.";
if ($bp_diastolic < 60 || $bp_diastolic > 100)     $errors[] = "Diastolic blood pressure must be between 60–100 mmHg.";
if (!empty($last_donation)) {
    $days_since = floor((time() - strtotime($last_donation)) / 86400);
    if ($days_since < 90) $errors[] = "Last donation was only $days_since days ago. Must be 90+ days.";
}
if (!$is_existing_donor) {
    if (strlen($password) < 8)        $errors[] = "Password must be at least 8 characters.";
    if ($password !== $confirm)        $errors[] = "Passwords do not match.";
}

if (!empty($errors)) {
    if (isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'msg' => implode("|", $errors)]);
        exit();
    }
    $errStr = urlencode(implode("|", $errors));
    header("Location: donor.php?status=error&msg=$errStr");
    exit();
}

// ── Insert or Update Donar table ───────────────────────────────
if ($is_existing_donor && $existing_donor_id > 0) {
    $sql = "UPDATE Donar SET 
            donar_weight = $weight,
            donar_hemoglobin = $hemoglobin,
            donar_bp_systolic = $bp_systolic,
            donar_bp_diastolic = $bp_diastolic,
            donar_temperature = $temperature,
            donar_last_donation = $last_donation_val,
            donar_medical_conditions = '$conditions'
            WHERE donar_id = $existing_donor_id";
    $success = $conn->query($sql);
    $donar_id = $existing_donor_id;
} else {
    $sql = "INSERT INTO Donar 
            (donar_name, donar_age, donar_gender, donar_blood_group, donar_contact, donar_address,
             donar_email, donar_weight, donar_hemoglobin, donar_bp_systolic, donar_bp_diastolic,
             donar_temperature, donar_last_donation, donar_medical_conditions)
            VALUES
            ('$name', $age, '$gender', '$blood_group', '$contact', '$address',
             '$email', $weight, $hemoglobin, $bp_systolic, $bp_diastolic,
             $temperature, $last_donation_val, '$conditions')";
    $success = $conn->query($sql);
    if ($success) {
        $donar_id = $conn->insert_id;
        // Also create a login account in the donors table
        $esc_name  = $conn->real_escape_string($name);
        $esc_email = $conn->real_escape_string($email);
        $esc_pass  = $conn->real_escape_string($password);
        $sql_login = "INSERT INTO donors (name, email, password, role)
                      VALUES ('$esc_name', '$esc_email', '$esc_pass', 'donor')";
        $conn->query($sql_login); 
    }
}

if ($success) {
    // Donation record and stock updates will be processed only after the admin validates and issues the certificate.

    $conn->query("CREATE TABLE IF NOT EXISTS Notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        donor_id INT,
        message TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_request_acceptance BOOLEAN DEFAULT FALSE
    )");

    // Dynamic schema expansion for donation schedules, admin processing, and certificates
    $schema_updates = [
        'is_donation_schedule'   => "ALTER TABLE Notifications ADD is_donation_schedule BOOLEAN DEFAULT FALSE",
        'donation_date'           => "ALTER TABLE Notifications ADD donation_date VARCHAR(100) DEFAULT NULL",
        'donation_location'       => "ALTER TABLE Notifications ADD donation_location TEXT DEFAULT NULL",
        'ref_donor_id'            => "ALTER TABLE Notifications ADD ref_donor_id INT DEFAULT NULL",
        'is_processed'            => "ALTER TABLE Notifications ADD is_processed BOOLEAN DEFAULT FALSE",
        'is_certificate'          => "ALTER TABLE Notifications ADD is_certificate BOOLEAN DEFAULT FALSE",
        'cert_donor_name'         => "ALTER TABLE Notifications ADD cert_donor_name VARCHAR(150) DEFAULT NULL",
        'cert_donor_age'          => "ALTER TABLE Notifications ADD cert_donor_age INT DEFAULT NULL",
        'cert_donor_blood_group'  => "ALTER TABLE Notifications ADD cert_donor_blood_group VARCHAR(10) DEFAULT NULL",
        'cert_donated_amount'     => "ALTER TABLE Notifications ADD cert_donated_amount INT DEFAULT NULL",
        'cert_donation_date'      => "ALTER TABLE Notifications ADD cert_donation_date DATE DEFAULT NULL",
        'cert_id'                 => "ALTER TABLE Notifications ADD cert_id VARCHAR(50) DEFAULT NULL",
        'cert_code'               => "ALTER TABLE Notifications ADD cert_code VARCHAR(50) DEFAULT NULL"
    ];
    foreach ($schema_updates as $col => $alter_query) {
        $check = $conn->query("SHOW COLUMNS FROM Notifications LIKE '$col'");
        if ($check && $check->num_rows == 0) {
            $conn->query($alter_query);
        }
    }

    $msg = "Thank you! You successfully registered to donate blood.";
    $conn->query("INSERT INTO Notifications (donor_id, message) VALUES ($donar_id, '$msg')");

    // Next donation schedule notification
    // Calculate Next Saturday PHP
    $today = new DateTime();
    $dayOfWeek = intval($today->format('w')); // 0=Sun, 6=Sat
    $daysUntilSaturday = (6 - $dayOfWeek + 7) % 7;
    if ($daysUntilSaturday === 0) {
        $daysUntilSaturday = 7;
    }
    $today->modify("+$daysUntilSaturday days");
    $next_sat = $today->format('l, F j, Y');

    $sched_msg = "Your next blood donation slot is assigned for $next_sat. Click here to view location & details.";
    $loc_details = "LifeLine Blood Bank, 123 Main Road, Hubli, Karnataka — 580001 (Phone: +91-9876543210)";
    
    $conn->query("INSERT INTO Notifications (donor_id, message, is_donation_schedule, donation_date, donation_location) 
                  VALUES ($donar_id, '$sched_msg', 1, '$next_sat', '$loc_details')");

    // Admin notification about the registration with ref_donor_id included
    $admin_msg = $conn->real_escape_string("Donor $name is registered to donate blood. Details — Blood Group: $blood_group, Age: $age, Contact: $contact, Location: $address");
    $conn->query("INSERT INTO Notifications (donor_id, message, ref_donor_id) VALUES (9999, '$admin_msg', $donar_id)");

    if (isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'donor_id' => $donar_id,
            'name' => $name,
            'age' => $age,
            'blood_group' => $blood_group,
            'amount' => $donated_amount,
            'date' => $today_date
        ]);
        $conn->close();
        exit();
    }

    $conn->close();
    header("Location: donor.php?status=success&name=" . urlencode($name));
    exit();
} else {
    if (isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'msg' => 'Database error: ' . $conn->error]);
        $conn->close();
        exit();
    }
    $err = urlencode("Database error: " . $conn->error);
    $conn->close();
    header("Location: donor.php?status=error&msg=$err");
    exit();
}
?>
