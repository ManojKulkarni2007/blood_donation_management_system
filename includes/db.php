<?php
// Database connection file
$host = "localhost";
$user = "root";       // change if you set a username
$pass = "";           // change if you set a password
$db   = "BDMS"; // your database name

$conn = mysqli_connect($host, $user, $pass, $db, 3307);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
