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
  <title>LifeLine Blood Bank</title>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="theme.js"></script>
  <style>
    :root {
      --primary: #c62828;
      --secondary: #d32f2f;
      --accent: #ff5252;
      --bg-main: #fff5f5;
      --bg-card: rgba(255, 255, 255, 0.85);
      --text-main: #2d1313;
      --text-muted: #6b5959;
      --border-color: rgba(198, 40, 40, 0.1);
      --header-bg: rgba(255, 245, 245, 0.85);
      --footer-bg: rgba(198, 40, 40, 0.05);
    }

    html.dark-theme, body.dark-theme {
      --primary: #ff2a2a;
      --secondary: #b71c1c;
      --accent: #ff6b6b;
      --bg-main: #0a0505;
      --bg-card: rgba(255, 255, 255, 0.03);
      --text-main: #fdfdfd;
      --text-muted: #a09898;
      --border-color: rgba(255,255,255,0.05);
      --header-bg: rgba(10, 5, 5, 0.6);
      --footer-bg: rgba(0,0,0,0.4);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Outfit', sans-serif;
      background-color: var(--bg-main);
      background-image: 
        radial-gradient(circle at 15% 50%, rgba(183, 28, 28, 0.08) 0%, transparent 50%),
        radial-gradient(circle at 85% 30%, rgba(229, 57, 53, 0.05) 0%, transparent 50%);
      color: var(--text-main);
      overflow-x: hidden;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      transition: background-color 0.5s ease, color 0.5s ease;
    }

    /* Ambient animated background elements */
    .ambient-orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(100px);
      z-index: -1;
      animation: orbFloat 15s infinite alternate ease-in-out;
    }
    .orb-1 {
      width: 400px; height: 400px;
      background: var(--primary);
      top: 10%; left: -10%;
      opacity: 0.15;
    }
    .orb-2 {
      width: 500px; height: 500px;
      background: var(--accent);
      bottom: 20%; right: -15%;
      opacity: 0.1;
      animation-delay: -7s;
    }

    @keyframes orbFloat {
      0% { transform: translate(0, 0) scale(1); }
      50% { transform: translate(50px, 100px) scale(1.2); }
      100% { transform: translate(-30px, -50px) scale(0.9); }
    }

    /* Modern Glass Header with High Contrast Styles */
    .site-header {
      background: linear-gradient(135deg, #7b0000 0%, #b71c1c 100%);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-bottom: 2px solid rgba(255, 255, 255, 0.15);
      box-shadow: 0 4px 30px rgba(123, 0, 0, 0.15);
      padding: 15px 50px;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      position: fixed;
      width: 100%;
      min-height: 90px;
      top: 0;
      z-index: 1000;
      animation: slideDown 1s cubic-bezier(0.16, 1, 0.3, 1);
      transition: background 0.5s ease, border-color 0.5s ease, box-shadow 0.5s ease;
    }
    body.dark-theme .site-header {
      background: rgba(15, 8, 8, 0.95);
      border-bottom: 2px solid #ff2a2a;
      box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
    }
    .header-logo {
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      align-items: center;
      gap: 20px;
      cursor: pointer;
    }
    .header-logo img {
      width: 75px;
      height: 75px;
      border-radius: 18px;
      box-shadow: 0 0 25px rgba(255, 255, 255, 0.25);
      transition: transform 0.3s ease;
    }
    body.dark-theme .header-logo img {
      box-shadow: 0 0 25px rgba(255, 42, 42, 0.4);
    }
    .header-logo:hover img {
      transform: rotate(5deg) scale(1.1);
    }
    .header-logo h2 {
      color: #ffffff;
      font-size: 34px;
      font-weight: 800;
      letter-spacing: -1px;
      transition: color 0.5s ease;
    }
    body.dark-theme .header-logo h2 {
      color: #ffffff;
      text-shadow: 0 0 10px rgba(255, 42, 42, 0.3);
    }
    .header-actions {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    .contact-pill {
      background: rgba(255, 255, 255, 0.15);
      color: #ffffff;
      padding: 8px 16px;
      border-radius: 20px;
      border: 1px solid rgba(255, 255, 255, 0.25);
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      font-weight: 600;
      transition: all 0.5s ease;
    }
    body.dark-theme .contact-pill {
      background: rgba(255, 42, 42, 0.1);
      color: #ff6b6b;
      border: 1px solid rgba(255, 42, 42, 0.25);
    }
    
    /* Theme Toggle Button */
    .theme-toggle {
      background: transparent;
      border: 1px solid rgba(255, 255, 255, 0.3);
      color: #ffffff;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      transition: all 0.3s ease;
    }
    .theme-toggle:hover {
      background: rgba(255, 255, 255, 0.2);
      transform: scale(1.1);
    }
    body.dark-theme .theme-toggle {
      border: 1px solid rgba(255, 42, 42, 0.3);
      color: #ff2a2a;
    }
    body.dark-theme .theme-toggle:hover {
      background: rgba(255, 42, 42, 0.15);
    }
    .moon-icon { display: none; }
    .sun-icon { display: block; }
    
    body.dark-theme .moon-icon { display: block; }
    body.dark-theme .sun-icon { display: none; }


    /* Main Hero Area */
    .main-area {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 150px 20px 80px;
      text-align: center;
      position: relative;
      z-index: 10;
    }

    .hero-badge {
      background: rgba(229, 57, 53, 0.1);
      border: 1px solid rgba(229, 57, 53, 0.2);
      color: var(--secondary);
      padding: 8px 20px;
      border-radius: 30px;
      font-size: 14px;
      font-weight: 600;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-bottom: 25px;
      opacity: 1;
    }
    body.dark-theme .hero-badge { color: var(--accent); }

    h1 {
      font-size: 72px;
      font-weight: 900;
      line-height: 1.1;
      margin-bottom: 20px;
      letter-spacing: -2px;
      opacity: 1;
    }
    .gradient-text {
      color: var(--text-main);
      transition: color 0.5s ease;
    }
    .highlight-text {
      background: linear-gradient(135deg, var(--accent) 0%, var(--primary) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      text-shadow: 0 0 30px rgba(229, 57, 53, 0.3);
    }

    .details {
      font-size: 18px;
      color: var(--text-muted);
      max-width: 600px;
      margin-bottom: 60px;
      line-height: 1.7;
      opacity: 1;
      transition: color 0.5s ease;
    }

    /* Premium Navigation Row */
    .nav-row {
      display: flex;
      flex-direction: row;
      flex-wrap: nowrap;
      justify-content: center;
      gap: 20px;
      width: 100%;
      max-width: 1200px;
      overflow-x: auto;
      padding: 20px 10px;
      animation: fadeUp 1s cubic-bezier(0.16, 1, 0.3, 1) both 0.5s;
    }
    .nav-row::-webkit-scrollbar { display: none; }

    .nav-card {
      background: var(--bg-card);
      backdrop-filter: blur(10px);
      border: 1px solid var(--border-color);
      padding: 30px 25px;
      border-radius: 24px;
      text-decoration: none;
      color: var(--text-main);
      font-weight: 600;
      font-size: 17px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 15px;
      flex: 1;
      min-width: 180px;
      position: relative;
      overflow: hidden;
      transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 10px 30px rgba(0,0,0,0.03);
    }

    .nav-card::after {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      border-radius: 24px;
      padding: 2px;
      background: linear-gradient(135deg, transparent 40%, var(--primary) 50%, transparent 60%);
      background-size: 300% 300%;
      -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
      -webkit-mask-composite: xor;
      mask-composite: exclude;
      opacity: 0;
      transition: opacity 0.5s ease;
    }

    .card-icon {
      font-size: 38px;
      font-style: normal;
      background: rgba(183, 28, 28, 0.05);
      width: 70px; height: 70px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 20px;
      transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    body.dark-theme .card-icon { background: rgba(255, 255, 255, 0.05); }

    .nav-card:hover {
      transform: translateY(-12px) scale(1.02);
      background: var(--bg-card);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1), 0 0 20px rgba(229, 57, 53, 0.1);
    }
    body.dark-theme .nav-card:hover {
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 20px rgba(255, 42, 42, 0.2);
    }
    
    .nav-card:hover::after {
      opacity: 1;
      animation: borderFlow 2s linear infinite;
    }
    .nav-card:hover .card-icon {
      transform: scale(1.15) rotate(5deg);
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
      box-shadow: 0 10px 20px rgba(183, 28, 28, 0.3);
    }

    @keyframes borderFlow {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Ticket-Style Camps Section */
    .camps-section {
      max-width: 1200px;
      margin: 0 auto 100px;
      padding: 0 20px;
      width: 100%;
      position: relative;
      z-index: 10;
    }

    .section-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 40px;
    }

    .section-title {
      font-size: 36px;
      font-weight: 800;
      color: var(--text-main);
      transition: color 0.5s ease;
    }

    .section-title span {
      color: var(--primary);
    }
    body.dark-theme .section-title span { color: var(--accent); }

    .camps-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 30px;
    }

    .camp-ticket {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 20px;
      padding: 35px;
      position: relative;
      overflow: hidden;
      transition: all 0.4s ease;
      display: flex;
      flex-direction: column;
      box-shadow: 0 5px 15px rgba(0,0,0,0.02);
    }
    
    .camp-ticket::before {
      content: '';
      position: absolute;
      top: 0; left: 0;
      width: 6px; height: 100%;
      background: linear-gradient(to bottom, var(--accent), var(--primary));
    }

    .camp-ticket:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }
    body.dark-theme .camp-ticket:hover {
       box-shadow: 0 15px 35px rgba(0,0,0,0.5);
       background: rgba(255,255,255,0.06);
    }

    .ticket-date {
      display: inline-block;
      padding: 6px 12px;
      background: rgba(229, 57, 53, 0.1);
      color: var(--secondary);
      border-radius: 8px;
      font-size: 13px;
      font-weight: 700;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-bottom: 20px;
      align-self: flex-start;
      transition: all 0.5s ease;
    }
    body.dark-theme .ticket-date {
      background: rgba(255, 42, 42, 0.15);
      color: var(--accent);
    }

    .ticket-location {
      font-size: 24px;
      font-weight: 700;
      margin-bottom: 15px;
      color: var(--text-main);
      transition: color 0.5s ease;
    }

    .ticket-desc {
      font-size: 15px;
      color: var(--text-muted);
      line-height: 1.6;
      flex-grow: 1;
      transition: color 0.5s ease;
    }

    /* Modern Footer with High Contrast Styles */
    .site-footer {
      background: #1c0a0a;
      border-top: 2px solid #b71c1c;
      padding: 35px 20px;
      text-align: center;
      margin-top: auto;
      position: relative;
      z-index: 10;
      box-shadow: 0 -4px 30px rgba(0, 0, 0, 0.15);
      transition: background 0.5s ease, border-color 0.5s ease, box-shadow 0.5s ease;
    }
    body.dark-theme .site-footer {
      background: #030101;
      border-top: 2px solid rgba(255, 42, 42, 0.25);
      box-shadow: 0 -4px 30px rgba(0, 0, 0, 0.8);
    }
    .site-footer p {
      margin: 6px 0;
      font-size: 14.5px;
      color: #e0d0d0;
      transition: color 0.5s ease;
      letter-spacing: 0.3px;
    }
    body.dark-theme .site-footer p {
      color: #a09898;
    }
    .site-footer strong {
      color: #ffffff;
      transition: color 0.5s ease;
      font-weight: 700;
    }
    body.dark-theme .site-footer strong {
      color: #ff6b6b;
      text-shadow: 0 0 8px rgba(255, 42, 42, 0.2);
    }

    /* Hero Content Layout */
    .hero-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 40px;
      max-width: 1200px;
      margin: 0 auto 60px;
      text-align: center;
    }
    .hero-text {
      width: 100%;
      max-width: 1000px;
      display: flex;
      flex-direction: column;
      align-items: center;
      z-index: 10;
    }
    .hero-visual {
      width: 100%;
      max-width: 600px;
      min-height: 450px;
      display: flex;
      justify-content: center;
      align-items: center;
      perspective: 1000px;
      z-index: 5;
      opacity: 1;
    }
    
    .image-collage {
      position: relative;
      width: 100%;
      max-width: 500px;
      height: 420px;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    
    .collage-bg-blur {
      position: absolute;
      width: 350px;
      height: 350px;
      background: radial-gradient(circle, var(--accent) 0%, transparent 70%);
      opacity: 0.15;
      filter: blur(50px);
      z-index: 0;
      animation: pulseDrop 4s infinite alternate;
    }

    .collage-img {
      position: absolute;
      border-radius: 24px;
      box-shadow: 0 25px 50px rgba(0,0,0,0.15);
      border: 4px solid var(--bg-card);
      transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
      object-fit: cover;
    }
    
    body.dark-theme .collage-img {
      box-shadow: 0 25px 50px rgba(0,0,0,0.6);
      border-color: rgba(255,255,255,0.05);
    }

    .img-back {
      width: 75%;
      height: 280px;
      top: 5%;
      right: 0%;
      opacity: 0.5;
      transform: scale(0.9) rotate(8deg) translateZ(-50px);
      filter: blur(4px);
      z-index: 1;
    }

    .img-front {
      width: 85%;
      height: 320px;
      bottom: 5%;
      left: 0%;
      transform: rotate(-4deg) translateZ(0);
      z-index: 3;
    }

    /* Interactive Hover Effect */
    .image-collage:hover .img-back {
      transform: scale(0.95) rotate(0deg) translateZ(0) translateX(-40px);
      opacity: 1;
      filter: blur(0px);
      z-index: 4;
    }
    
    .image-collage:hover .img-front {
      transform: scale(0.9) rotate(0deg) translateZ(-50px) translateX(40px);
      opacity: 0.4;
      filter: blur(4px);
      z-index: 1;
    }
    
    @media (max-width: 900px) {
      .hero-container {
        gap: 20px;
      }
      .image-collage {
        height: 350px;
        margin-top: 10px;
      }
    }
    
    /* Entrance Animations */
    @keyframes slideDown {
      from { opacity: 0; transform: translateY(-100%); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(40px); }
      to { opacity: 1; transform: translateY(0); }
    }

  </style>
</head>
<body>

  <!-- Ambient Glowing Orbs -->
  <div class="ambient-orb orb-1"></div>
  <div class="ambient-orb orb-2"></div>

  <!-- Ultra-Modern Sticky Header -->
  <header class="site-header">
    <div class="header-logo">
      <img src="logo.jpeg" alt="Logo">
      <h2>LifeLine</h2>
    </div>
    <div class="header-actions">
      <div class="contact-pill">📞 +91-9876543210</div>
      <button class="theme-toggle" aria-label="Toggle Dark Mode">
        <span class="moon-icon">🌙</span>
        <span class="sun-icon">☀️</span>
      </button>
    </div>
  </header>

  <!-- Cinematic Hero Area -->
  <section class="main-area">
    
    <div class="hero-container">
      <div class="hero-text">
        <div class="hero-badge" style="margin-bottom: 20px;">Save Lives Today</div>
        <h1 style="text-align: center; font-size: 64px;">
          <span class="gradient-text">Give Blood,</span>
          <span class="highlight-text">Give Life.</span>
        </h1>
        <div class="details" style="text-align: center; margin-bottom: 20px; max-width: 700px;">
          We connect willing donors with patients in urgent need. Join our community and become a real-world hero. Every drop counts.
        </div>
      </div>
      
      <!-- Advanced Glassmorphism Cards -->
      <div class="nav-row" style="margin-bottom: 40px; margin-top: -20px;">
        <a href="home.php" class="nav-card">
          <span class="card-icon">📊</span>
          <span>Dashboard</span>
        </a>
        <a href="transactions.php" class="nav-card">
          <span class="card-icon">📋</span>
          <span>View History</span>
        </a>
        <a href="staff.php" class="nav-card">
          <span class="card-icon">👨‍⚕️</span>
          <span>Staff Details</span>
        </a>
        <a href="login.php" class="nav-card">
          <span class="card-icon">👤</span>
          <span>Login Portal</span>
        </a>
        <a href="#camps" class="nav-card">
          <span class="card-icon">⛺</span>
          <span>Upcoming Camps</span>
        </a>
      </div>

      <div class="hero-visual">
        <div class="image-collage">
          <div class="collage-bg-blur"></div>
          <img src="recipient_image.jpg" alt="Blood Recipient" class="collage-img img-back">
          <img src="donor_image.jpg" alt="Blood Donor" class="collage-img img-front">
        </div>
      </div>
    </div>


  </section>

  <!-- Premium Ticket-Style Camps Section -->
  <section id="camps" class="camps-section">
    <div class="section-header">
      <h2 class="section-title">Upcoming <span>Donation Camps</span></h2>
    </div>
    
    <div class="camps-grid">
      
      <div class="camp-ticket">
        <div class="ticket-date">April 25, 2026</div>
        <div class="ticket-location">KLE Hospital Auditorium, Hubli</div>
        <div class="ticket-desc">A mega blood donation drive in collaboration with local NGOs. Comprehensive health screenings, refreshments, and donor certificates will be provided.</div>
      </div>

      <div class="camp-ticket">
        <div class="ticket-date">May 12, 2026</div>
        <div class="ticket-location">BVB College Campus, Vidyanagar</div>
        <div class="ticket-desc">Annual youth blood donation initiative. Special focus on educating first-time donors to help combat summer blood shortages across the state.</div>
      </div>

      <div class="camp-ticket">
        <div class="ticket-date">June 05, 2026</div>
        <div class="ticket-location">Rotary Club Hall, Dharwad</div>
        <div class="ticket-desc">Corporate and community donation event. Specialized health checkups, hemoglobin screening, and professional consultation provided completely free.</div>
      </div>

    </div>
  </section>

  <!-- Clean Footer -->
  <footer class="site-footer">
    <p><strong>LifeLine Blood Bank</strong> &nbsp;|&nbsp; 143-2nd stage, Banashankari layout, Keshwapur, Hubli</p>
    <p>© 2026 All Rights Reserved. Designed for humanity.</p>
  </footer>

</body>
</html>
