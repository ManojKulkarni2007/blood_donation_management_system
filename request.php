<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: request_blood.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "BDMS", 3307);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$patient_name = isset($_POST['patient_name']) ? $_POST['patient_name'] : '';
$blood_group = isset($_POST['blood_group']) ? $_POST['blood_group'] : '';
$units = isset($_POST['units']) ? $_POST['units'] : '';
$hospital = isset($_POST['hospital']) ? $_POST['hospital'] : '';
$contact = isset($_POST['contact']) ? $_POST['contact'] : '';

$sql = "INSERT INTO Requests (patient_name, blood_group, units, hospital, contact)
        VALUES ('$patient_name', '$blood_group', '$units', '$hospital', '$contact')";

if ($conn->query($sql) === TRUE) {
    $conn->close();
    header("Location: request_blood.php?status=success");
    exit();
} else {
    $error = urlencode($conn->error);
    $conn->close();
    header("Location: request_blood.php?status=error&msg=$error");
    exit();
}
?>
