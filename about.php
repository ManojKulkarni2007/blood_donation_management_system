<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - Blood Donation Management System</title>
  <link rel="stylesheet" href="style.css?v=6">
  <style>
    .about-container {
      max-width: 860px;
      margin: 0 auto;
    }
    .about-intro {
      background: #fff;
      border-radius: 14px;
      padding: 30px 36px;
      box-shadow: 0 4px 16px rgba(183,28,28,0.09);
      margin-bottom: 28px;
      font-size: 15.5px;
      line-height: 1.75;
      color: #444;
    }
    .about-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 28px;
    }
    .about-card {
      background: #fff;
      border-radius: 14px;
      border-left: 5px solid #e53935;
      padding: 24px 26px;
      box-shadow: 0 4px 16px rgba(183,28,28,0.08);
    }
    .about-card h3 { color: #b71c1c; font-size: 17px; margin-bottom: 10px; }
    .about-card p  { font-size: 14px; color: #555; line-height: 1.65; }
    .why-box {
      background: #fff;
      border-radius: 14px;
      padding: 28px 36px;
      box-shadow: 0 4px 16px rgba(183,28,28,0.09);
      margin-bottom: 28px;
    }
    .why-box h2 { color: #b71c1c; font-size: 20px; margin-bottom: 14px; }
    .why-box p  { font-size: 14.5px; color: #555; line-height: 1.7; margin-bottom: 14px; }
    .why-box ul { padding-left: 18px; }
    .why-box ul li { margin-bottom: 10px; font-size: 14.5px; color: #444; line-height: 1.55; }
    @media(max-width:700px){ .about-grid{ grid-template-columns:1fr; } }
  </style>
</head>
<body>

  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <div class="page-header">
      <a href="index.php" class="back-link">← Back to Home</a>
      <h1>ℹ️ About Us</h1>
      <p>Learn about the Blood Donation Management System and our mission.</p>
    </div>

    <div class="page-body">
      <div class="about-container">

        <div class="about-intro">
          Welcome to the <strong>Blood Donation Management System (BDMS)</strong>. We are a dedicated platform built to bridge the gap between voluntary blood donors and recipients in need of urgent life-saving blood. Our mission is to leverage technology to streamline the blood procurement process, making it fast, transparent, and completely free of charge.
        </div>

        <div class="section-title">🎯 Our Core Values</div>
        <div class="about-grid">
          <div class="about-card">
            <h3>🌟 Our Vision</h3>
            <p>To create a community where nobody suffers due to the non-availability of matching blood groups, ensuring every hospital and patient has instant access to safe blood donor networks.</p>
          </div>
          <div class="about-card">
            <h3>📌 Our Strategy</h3>
            <p>By registering active voluntary donors and organizing them by location and blood group, we facilitate direct, real-time connectivity between families in emergency need and willing donors.</p>
          </div>
          <div class="about-card">
            <h3>🤝 Community Driven</h3>
            <p>Every donor registered on our platform is a volunteer. We rely on the goodwill of compassionate individuals who believe in saving lives without any monetary exchange.</p>
          </div>
          <div class="about-card">
            <h3>🔒 Privacy First</h3>
            <p>Donor information is managed securely. Contact details are only accessible to verified users seeking blood in emergencies, ensuring trust and safety for all parties.</p>
          </div>
        </div>

        <div class="why-box">
          <h2>Why Choose BDMS?</h2>
          <p>Traditional blood banks can often face logistical delays or critical group shortages. Our platform enables families to find and connect with matching blood groups immediately.</p>
          <ul>
            <li><strong>100% Free</strong> — A non-commercial, purely volunteer-driven utility.</li>
            <li><strong>Real-time Matching</strong> — Instantly search and locate matching donors in your city.</li>
            <li><strong>Privacy Focused</strong> — Donor contact details are managed safely and securely.</li>
            <li><strong>24/7 Helpdesk</strong> — Our support team is available round the clock for emergencies.</li>
          </ul>
        </div>

        <div class="why-box">
          <h2>🩸 Important Facts About Blood Donation</h2>
          <p>Blood is the most precious gift that anyone can give to another person — the gift of life. A decision to donate your blood can save a life, or even several if your blood is separated into its components — red cells, platelets and plasma.</p>
          <p>One donation can save up to <strong>3 lives</strong>. The body replenishes donated blood within <strong>24–48 hours</strong>. Healthy adults can donate safely every <strong>3 months</strong>.</p>
        </div>

      </div>
    </div>

    <footer>
      <p>📞 +91-9876543210 &nbsp;|&nbsp; ✉️ <a href="mailto:support@bdms.com">support@bdms.com</a></p>
      <p>© 2026 Blood Donation Management System. All Rights Reserved.</p>
    </footer>
  </div>

</body>
</html>
