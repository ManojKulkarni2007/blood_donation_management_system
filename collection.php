<?php
$conn = new mysqli("localhost", "root", "", "BDMS", 3307);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$donar_id = $_POST['donar_id'];
$blood_group = $_POST['blood_group'];
$units = $_POST['units'];
$collection_date = $_POST['collection_date'];

$sql = "INSERT INTO Blood_Collection (donar_id, blood_group, units, collection_date)
        VALUES ('$donar_id', '$blood_group', '$units', '$collection_date')";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color:green;'>Blood collection recorded successfully!</p>";
} else {
    echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
}
$conn->close();
?>
