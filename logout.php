<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Logged Out - LifeLine Blood Bank</title>
  <link rel="stylesheet" href="style.css?v=7">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Outfit', sans-serif;
    }
    .logout-container {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 20px;
      flex: 1;
    }
    .logout-card {
      background: var(--bg-card);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border-radius: 24px;
      padding: 50px 40px;
      text-align: center;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
      border: 1px solid var(--border-color);
      max-width: 440px;
      width: 100%;
    }
    .logout-icon {
      width: 80px; height: 80px;
      background: linear-gradient(135deg, #e53935, #b71c1c);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 24px;
      font-size: 38px; color: #fff;
      box-shadow: 0 4px 15px rgba(229, 57, 53, 0.35);
    }
    .logout-card h2 {
      font-size: 26px;
      font-weight: 800;
      color: var(--text-main);
      margin-bottom: 12px;
    }
    .logout-card p {
      font-size: 15.5px;
      color: var(--text-muted);
      margin-bottom: 6px;
    }
  </style>
</head>
<body>
  <?php include 'sidebar.php'; ?>
  <div class="main-content">
    <div class="logout-container">
      <div class="logout-card">
        <div class="logout-icon">✓</div>
        <h2>Logged Out Successfully</h2>
        <p>You have been safely signed out of your account.</p>
        <p style="font-size: 13px; color: #aaa; margin-top: 10px;">Redirecting to home page...</p>
      </div>
    </div>
  </div>
  <script>
    // Immediate execution in head block to prevent layout theme flashing
    if (localStorage.getItem('theme') === 'dark') {
        document.documentElement.classList.add('dark-theme');
    }
    
    setTimeout(() => {
      window.location.href = "index.php";
    }, 2000);
  </script>
  <script src="theme.js"></script>
</body>
</html>
