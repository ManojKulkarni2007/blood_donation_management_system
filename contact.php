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
  <title>Contact Us - Blood Donation Management System</title>
  <link rel="stylesheet" href="style.css?v=6">
  <style>
    .contact-grid {
      display: grid;
      grid-template-columns: 1fr 1.3fr;
      gap: 28px;
      max-width: 960px;
      margin: 0 auto;
    }
    .contact-info-col h2 {
      color: #b71c1c;
      font-size: 20px;
      font-weight: 700;
      margin-bottom: 12px;
    }
    .contact-info-col p {
      font-size: 14.5px;
      color: #555;
      line-height: 1.7;
      margin-bottom: 22px;
    }
    .contact-card {
      background: #fff;
      border-radius: 12px;
      border-left: 5px solid #e53935;
      padding: 16px 20px;
      margin-bottom: 14px;
      box-shadow: 0 3px 12px rgba(183,28,28,0.08);
      display: flex;
      align-items: flex-start;
      gap: 14px;
    }
    .contact-card .cc-icon {
      font-size: 24px;
      flex-shrink: 0;
      margin-top: 2px;
    }
    .contact-card h3 {
      color: #b71c1c;
      font-size: 14px;
      font-weight: 700;
      margin: 0 0 4px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .contact-card p {
      font-size: 13.5px;
      margin: 0;
      color: #444;
      line-height: 1.6;
    }
    /* Form column */
    .contact-form-col {
      background: #fff;
      border-radius: 16px;
      padding: 36px 36px 30px;
      box-shadow: 0 6px 24px rgba(183,28,28,0.1);
    }
    .contact-form-col h2 {
      color: #b71c1c;
      font-size: 22px;
      font-weight: 800;
      margin-bottom: 22px;
    }
    .contact-form-col label {
      display: block;
      font-weight: 600;
      font-size: 13px;
      color: #444;
      margin-bottom: 6px;
    }
    .contact-form-col input,
    .contact-form-col textarea {
      width: 100%;
      padding: 11px 14px;
      border: 1.5px solid #e0e0e0;
      border-radius: 8px;
      font-size: 14px;
      font-family: inherit;
      background: #fafafa;
      transition: border-color 0.2s, box-shadow 0.2s;
      margin-bottom: 16px;
      box-sizing: border-box;
    }
    .contact-form-col input:focus,
    .contact-form-col textarea:focus {
      outline: none;
      border-color: #e53935;
      box-shadow: 0 0 0 3px rgba(229,57,53,0.1);
      background: #fff;
    }
    .contact-form-col textarea {
      height: 130px;
      resize: vertical;
    }
    .send-btn {
      width: 100%;
      padding: 13px;
      background: linear-gradient(135deg, #e53935, #b71c1c);
      border: none;
      color: #fff;
      font-size: 15px;
      font-weight: 700;
      border-radius: 10px;
      cursor: pointer;
      box-shadow: 0 4px 16px rgba(229,57,53,0.35);
      transition: transform 0.2s, box-shadow 0.2s;
      display: block;
      margin: 0;
    }
    .send-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(229,57,53,0.45);
    }

    /* Success Modal */
    .modal-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.55);
      backdrop-filter: blur(4px);
      z-index: 9999;
      justify-content: center;
      align-items: center;
    }
    .modal-overlay.active { display: flex; }
    .modal-box {
      background: #fff;
      border-radius: 18px;
      padding: 48px 40px;
      max-width: 420px;
      width: 90%;
      text-align: center;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
      animation: popIn 0.35s cubic-bezier(0.34,1.56,0.64,1);
    }
    @keyframes popIn {
      from { transform: scale(0.7); opacity: 0; }
      to   { transform: scale(1);   opacity: 1; }
    }
    .modal-icon {
      width: 72px; height: 72px;
      background: linear-gradient(135deg, #e53935, #b71c1c);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 20px;
      font-size: 36px; color: #fff;
    }
    .modal-box h3 { color: #b71c1c; font-size: 22px; margin: 0 0 10px; }
    .modal-box p  { color: #555; font-size: 15px; margin: 0 0 28px; line-height: 1.6; }
    .modal-home-btn {
      display: inline-block;
      padding: 12px 36px;
      background: linear-gradient(135deg, #e53935, #b71c1c);
      color: #fff; font-size: 15px; font-weight: 700;
      border-radius: 10px; text-decoration: none;
      box-shadow: 0 4px 14px rgba(229,57,53,0.4);
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .modal-home-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(229,57,53,0.5);
    }

    @media (max-width: 768px) {
      .contact-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

  <?php include 'sidebar.php'; ?>

  <!-- Success Modal -->
  <div class="modal-overlay" id="successModal">
    <div class="modal-box">
      <div class="modal-icon">&#10003;</div>
      <h3>Message Sent!</h3>
      <p>Thank you for contacting us.<br>We will get back to you shortly.</p>
      <a href="index.php" class="modal-home-btn">&#8592; Go Back to Home</a>
    </div>
  </div>

  <div class="main-content">
    <div class="page-header">
      <a href="index.php" class="back-link">← Back to Home</a>
      <h1>📞 Contact Us</h1>
      <p>Get in touch with our team for support, queries, or emergencies.</p>
    </div>

    <div class="page-body">
      <div class="contact-grid">

        <!-- Left: Info -->
        <div class="contact-info-col">
          <h2>Get in Touch</h2>
          <p>Have questions about blood donation or need support? Reach out to our dedicated volunteer support desk using the details below or send a message through the contact form.</p>

          <div class="contact-card">
            <div class="cc-icon">📞</div>
            <div>
              <h3>Call Us</h3>
              <p>+91-9876543210<br>Mon – Sun (24/7 Helpline)</p>
            </div>
          </div>

          <div class="contact-card">
            <div class="cc-icon">✉️</div>
            <div>
              <h3>Email Support</h3>
              <p>support@bdms.com<br>info@bdms.org</p>
            </div>
          </div>

          <div class="contact-card">
            <div class="cc-icon">🏥</div>
            <div>
              <h3>Helpline Center</h3>
              <p>123 Life-Line Complex,<br>Health Avenue, New Delhi, India</p>
            </div>
          </div>

          <div class="contact-card">
            <div class="cc-icon">🕐</div>
            <div>
              <h3>Working Hours</h3>
              <p>Monday – Sunday<br>Available 24 hours a day</p>
            </div>
          </div>
        </div>

        <!-- Right: Form -->
        <div class="contact-form-col">
          <h2>✉️ Send a Message</h2>
          <form action="#" method="POST" onsubmit="document.getElementById('successModal').classList.add('active'); return false;">
            <label for="name">Your Name</label>
            <input type="text" id="name" placeholder="Enter your full name" required>

            <label for="email">Email Address</label>
            <input type="email" id="email" placeholder="you@example.com" required>

            <label for="subject">Subject</label>
            <input type="text" id="subject" placeholder="What is this about?" required>

            <label for="message">Message</label>
            <textarea id="message" placeholder="Write your message here..." required></textarea>

            <button type="submit" class="send-btn">📨 Send Message</button>
          </form>
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
