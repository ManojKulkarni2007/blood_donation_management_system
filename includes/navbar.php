<?php
session_start();
?>
<div class="navbar">
  <a href="index.php">Home</a>
  <a href="donor.html">Donor</a>
  <a href="recipient.html">Recipient</a>
  <a href="collect.html">Collection</a>
  <a href="stock.html">Stock</a>
  <a href="issue.html">Issue</a>
  <a href="match.html">Match</a>
  <a href="staff.html">Staff</a>
  <a href="transaction.html">Transactions</a>
  <a href="about.php">About</a>
  <a href="contact.php">Contact</a>

  <?php
  // Show logout only if logged in
  if (isset($_SESSION['donor_id'])) {
      echo '<a href="logout.php">Logout</a>';
  }
  ?>
</div>
