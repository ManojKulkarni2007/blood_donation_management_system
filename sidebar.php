<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current = basename($_SERVER['PHP_SELF']);
function navLink($href, $icon, $label, $current) {
    $active = (basename($href) === $current) ? ' active' : '';
    return "<li><a href=\"$href\" class=\"$active\"><span class=\"nav-icon\">$icon</span><span>$label</span></a></li>";
}
?>
<script src="theme.js"></script>
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="sidebar-logo-icon">🩸</div>
    <span>Blood Donation<br>Management System</span>
    
    <?php if (isset($_SESSION['donor_name'])): ?>
      <div class="sidebar-user-badge" style="margin-top: 15px; padding: 6px 14px; background: rgba(255,255,255,0.12); border-radius: 20px; font-size: 12px; font-weight: 700; color: #fff; display: inline-flex; align-items: center; gap: 8px; box-shadow: inset 0 0 8px rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.15);">
        <span class="user-avatar" style="font-size: 13px;">👤</span>
        <span class="user-name" style="max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($_SESSION['donor_name']) ?></span>
      </div>
    <?php endif; ?>
  </div>
  <nav>
    <ul>
      <?= navLink('index.php',       '🌐', 'Front Page',      $current) ?>
      <?= navLink('home.php',        '📊', 'Dashboard',       $current) ?>
      
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'donor'): ?>
        <?= navLink('donor.php',       '🩸', 'Donate Blood',    $current) ?>
        <?= navLink('donor_history.php', '📋', 'My History',      $current) ?>
        <?= navLink('notifications.php', '🔔', 'Notifications',   $current) ?>
        <?= navLink('request_blood.php','🏥', 'Request Blood',   $current) ?>
      <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <?= navLink('donor.php',       '🩸', 'Register Donor',  $current) ?>
        <?= navLink('search_donor.php', '🔍', 'Search Donors',   $current) ?>
        <?= navLink('notifications.php', '🔔', 'Notifications',   $current) ?>
        <?= navLink('transactions.php', '📋', 'All Transactions', $current) ?>
      <?php else: ?>
        <?= navLink('about.php',       'ℹ️',  'About',           $current) ?>
      <?php endif; ?>
      
      <?php if (isset($_SESSION['donor_id'])): ?>
        <?= navLink('logout.php',      '🔓', 'Logout',           $current) ?>
      <?php else: ?>
        <?= navLink('login.php',       '👤', 'Login / Register', $current) ?>
      <?php endif; ?>
      
      <?= navLink('contact.php',     '📞', 'Contact',         $current) ?>
    </ul>
  </nav>
  <div class="sidebar-theme-toggle">
    <button class="theme-toggle" aria-label="Toggle Dark Mode" style="background:transparent; border:none; color:white; font-size:18px; cursor:pointer; padding:10px; border-radius:5px; width:100%; text-align:left; display:flex; gap:10px; transition:0.3s;">
      <span class="moon-icon" style="display:none;">🌙 Switch to Light Mode</span>
      <span class="sun-icon">☀️ Switch to Dark Mode</span>
    </button>
  </div>
  <style>
    .dark-theme .moon-icon { display: block !important; }
    .dark-theme .sun-icon { display: none !important; }
    .theme-toggle:hover { background: rgba(0,0,0,0.2) !important; }

    /* ── Scrollable Sidebar ── */
    .sidebar {
      overflow-y: auto !important;
      overflow-x: hidden !important;
      scroll-behavior: smooth;
    }
    /* Custom slim scrollbar for sidebar */
    .sidebar::-webkit-scrollbar {
      width: 4px;
    }
    .sidebar::-webkit-scrollbar-track {
      background: rgba(255,255,255,0.05);
    }
    .sidebar::-webkit-scrollbar-thumb {
      background: rgba(255,255,255,0.25);
      border-radius: 4px;
    }
    .sidebar::-webkit-scrollbar-thumb:hover {
      background: rgba(255,255,255,0.45);
    }
  </style>
  <div class="sidebar-footer">© 2026 BDMS</div>
</aside>
