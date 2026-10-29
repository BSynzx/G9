<?php
$isAdmin = $isAdmin ?? false;
$isStaff = $isStaff ?? false;
$active  = $active  ?? '';
?>
  <aside class="sidebar" aria-label="Main navigation">
    <div class="sidebar__brand">
      <img class="sidebar__logo" src="../assets/images/logo.png" alt="Citizen Connect">
    </div>

    <nav class="nav" aria-label="Primary">
      <a class="nav__item<?php echo $active === 'dashboard' ? ' is-active' : ''; ?>" href="dashboard.php"<?php echo $active === 'dashboard' ? ' aria-current="page"' : ''; ?>><svg class="icon"><use href="#i-home"/></svg><span>Dashboard</span></a>
<?php if (!$isAdmin && !$isStaff): ?>
      <a class="nav__item<?php echo $active === 'report' ? ' is-active' : ''; ?>" href="report.php"<?php echo $active === 'report' ? ' aria-current="page"' : ''; ?>><svg class="icon"><use href="#i-edit"/></svg><span>Submit Complaint</span></a>
<?php endif; ?>
      <a class="nav__item" href="#"><svg class="icon"><use href="#i-file"/></svg><span>My Complaints</span></a>
      <a class="nav__item" href="#"><svg class="icon"><use href="#i-chart"/></svg><span>Complaint Status</span></a>
      <a class="nav__item" href="#"><svg class="icon"><use href="#i-megaphone"/></svg><span>Announcements</span></a>
      <a class="nav__item" href="#"><svg class="icon"><use href="#i-help"/></svg><span>Help &amp; Support</span></a>
      <a class="nav__item" href="#"><svg class="icon"><use href="#i-user"/></svg><span>Profile</span></a>
<?php if ($isAdmin): ?>
      <a class="nav__item" href="#"><svg class="icon"><use href="#i-user"/></svg><span>Manage Users</span></a>
      <a class="nav__item" href="#"><svg class="icon"><use href="#i-file"/></svg><span>All Complaints</span></a>
      <a class="nav__item" href="#"><svg class="icon"><use href="#i-chart"/></svg><span>System Reports</span></a>
<?php elseif ($isStaff): ?>
      <a class="nav__item" href="#"><svg class="icon"><use href="#i-file"/></svg><span>Assigned Complaints</span></a>
      <a class="nav__item" href="#"><svg class="icon"><use href="#i-gear"/></svg><span>Update Status</span></a>
<?php endif; ?>
      <a class="nav__item" href="../actions/logout.php"><svg class="icon"><use href="#i-logout"/></svg><span>Logout</span></a>
    </nav>

    <img class="sidebar__art" src="../assets/images/sidebar.png" alt="" aria-hidden="true">
  </aside>
  <label class="scrim" for="navToggle"></label>
