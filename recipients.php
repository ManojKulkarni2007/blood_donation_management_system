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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize inputs
    $name        = trim($_POST['recipient_name']);
    $age         = trim($_POST['recipient_age']);
    $gender      = trim($_POST['recipient_gender']);
    $blood_group = trim($_POST['recipient_blood_group']);
    $contact     = trim($_POST['recipient_contact']);
    $hospital    = trim($_POST['recipient_hospital']);

    // ✅ Prepared statement for security
    $stmt = $conn->prepare("INSERT INTO Recipient (recipient_name, recipient_age, recipient_gender, recipient_blood_group, recipient_contact, recipient_hospital) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissss", $name, $age, $gender, $blood_group, $contact, $hospital);

    if ($stmt->execute()) {
        $recipient_id = $stmt->insert_id; // auto-generated ID

        echo "<div style='text-align:center; margin-top:20px;'>";
        echo "<p style='color:green; font-size:28px; font-weight:bold;'>Recipient registered successfully!</p>";

        // Recipient ID in a copyable box
        echo "<p>Your Recipient ID is: 
                <input type='text' value='$recipient_id' readonly 
                       onclick='this.select();document.execCommand(\"copy\");' 
                       style='border:none;color:blue;font-weight:bold;font-size:22px;'>
              </p>";
        echo "<p style='font-size:12px;color:gray;'>Click the ID box to copy it.</p>";

        // Redirect back to dashboard after 5 seconds
        echo "<p style='color:gray;'>You will be redirected to the Dashboard in 5 seconds...</p>";
        echo "<script>
                setTimeout(function(){
                    window.location.href = 'index.php';
                }, 5000);
              </script>";

        // Manual navigation button
        echo "<form action='index.php' method='get'>
                <button type='submit' style='margin-top:10px;padding:8px 15px;'>Go Back to Home</button>
              </form>";
        echo "</div>";
    } else {
        echo "<p style='color:red;text-align:center;'>Error: " . $conn->error . "</p>";
    }

    $stmt->close();
}

$conn->close();

include("includes/footer.php");
include("includes/back.php");
?>
