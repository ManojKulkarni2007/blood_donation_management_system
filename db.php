<?php
$servername = "localhost";
$username   = "root";      // default XAMPP user
$password   = "";          // default XAMPP password is empty
$dbname     = "bdms"; // your database name
$conn = new mysqli($servername, $username, $password, $dbname,3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
