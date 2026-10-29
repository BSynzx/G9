<?php
$fullName    = $fullName    ?? '';
$role        = $role        ?? '';
$badgeClass  = $badgeClass  ?? 'pill--resolved';
$unreadCount = $unreadCount ?? 0;
?>
    <header class="topbar">
      <label class="icon-btn topbar__menu" for="navToggle" aria-label="Open menu"><svg class="icon"><use href="#i-menu"/></svg></label>

      <div class="topbar__actions">
        <a class="icon-btn" href="#" aria-label="Notifications">
          <svg class="icon"><use href="#i-bell"/></svg>
          <?php if ($unreadCount > 0): ?>
            <span class="badge-dot"><?php echo $unreadCount; ?></span>
          <?php endif; ?>
        </a>

        <a class="user-btn" href="#">
          <span class="avatar"><svg class="icon"><use href="#i-user"/></svg></span>
          <span class="user-btn__name"><?php echo htmlspecialchars($fullName); ?></span>
          <span class="pill <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($role); ?></span>
          <svg class="icon user-btn__chevron"><use href="#i-chevron-down"/></svg>
        </a>
      </div>
    </header>
