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
if (strlen($password) < 8)        $errors[] = "Password must be at least 8 characters.";
if ($password !== $confirm)        $errors[] = "Passwords do not match.";

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

// ── Insert into Donar table ───────────────────────────────────
$sql = "INSERT INTO Donar 
        (donar_name, donar_age, donar_gender, donar_blood_group, donar_contact, donar_address,
         donar_email, donar_weight, donar_hemoglobin, donar_bp_systolic, donar_bp_diastolic,
         donar_temperature, donar_last_donation, donar_medical_conditions)
        VALUES
        ('$name', $age, '$gender', '$blood_group', '$contact', '$address',
         '$email', $weight, $hemoglobin, $bp_systolic, $bp_diastolic,
         $temperature, $last_donation_val, '$conditions')";

if ($conn->query($sql) === TRUE) {
    $donar_id = $conn->insert_id;

    // Also create a login account in the donors table
    $esc_name  = $conn->real_escape_string($name);
    $esc_email = $conn->real_escape_string($email);
    $esc_pass  = $conn->real_escape_string($password);
    $sql_login = "INSERT INTO donors (name, email, password, role)
                  VALUES ('$esc_name', '$esc_email', '$esc_pass', 'donor')";
    $conn->query($sql_login); 

    // Insert donation record into Collection table
    $today_date = date('Y-m-d');
    $sql_collection = "INSERT INTO Collection (donar_id, collection_date, collection_quantity) 
                       VALUES ($donar_id, '$today_date', $donated_amount)";
    $conn->query($sql_collection);

    // Update Blood Stock automatically
    $sql_stock = "UPDATE Stock SET quantity = quantity + $donated_amount WHERE blood_group = '$blood_group'";
    $conn->query($sql_stock);

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
