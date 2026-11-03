<?php

require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Complaint.php';
require_once __DIR__ . '/../classes/Notification.php';

$auth = new Auth();
$user = $auth->currentUser();

if ($user === null) {
    header("Location: login.php");
    exit;
}

$userType = ($user instanceof Admin) ? 'Admin' : (($user instanceof FieldStaff) ? 'Staff' : 'Citizen');
$unreadCount = Notification::unreadCount((int)$user->getUserId(), $userType);

$fullName = $user->getFullName();
$role     = $user->getRole();

$isAdmin    = $user instanceof Admin;
$isStaff    = $user instanceof FieldStaff;
$badgeClass = $isAdmin ? 'pill--in-progress' : ($isStaff ? 'pill--pending' : 'pill--resolved');
$active     = 'dashboard';

if ($isAdmin || $isStaff) {
    $recentComplaints = Complaint::all();
} else {
    $recentComplaints = Complaint::forCitizen((int)$user->getUserId());
}

$totalComplaints = count($recentComplaints);
$pendingCount = 0;
$inProgressCount = 0;
$resolvedCount = 0;

foreach ($recentComplaints as $c) {
    if ($c->getStatus() === 'Pending') $pendingCount++;
    elseif ($c->getStatus() === 'In Progress') $inProgressCount++;
    elseif ($c->getStatus() === 'Resolved') $resolvedCount++;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Citizen Connect – Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=League+Spartan:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/dashboard.css">
</head>

<body>

<?php require_once __DIR__ . '/../includes/icons.php'; ?>

<input class="nav-toggle" type="checkbox" id="navToggle" aria-label="Toggle menu">

<div class="app">

<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

  <div class="app__main">

<?php require_once __DIR__ . '/../includes/topbar.php'; ?>

    <main class="content">

      <section class="hero" aria-labelledby="heroTitle">
        <div class="hero__text">
          <h1 class="hero__title" id="heroTitle">Welcome to Citizen Connect</h1>
          <p class="hero__tagline"><span class="blue">Your Voice.</span> <span class="yellow">Our Action.</span></p>
          <p class="hero__copy">Report your concerns, track your complaints, and stay updated with the latest news and services from your local government unit.</p>
        </div>

        <?php if (!$isAdmin && !$isStaff): ?>
        <div class="hero__cta">
          <a class="btn-cta" href="report.php">
            <svg class="icon"><use href="#i-pencil"/></svg>
            Submit a Complaint
            <svg class="icon"><use href="#i-arrow-right"/></svg>
          </a>
        </div>
        <?php endif; ?>

        <div class="hero__art">
          <img src="../assets/images/graphic1.png" alt="">
        </div>
      </section>

      <section aria-labelledby="qaTitle">
        <h2 class="section-title" id="qaTitle">Quick Actions</h2>
        <div class="quick">
        <?php if (!$isAdmin && !$isStaff): ?>
        <a class="quick__card" href="report.php">
          <span class="bubble tone-blue"><svg class="icon"><use href="#i-edit"/></svg></span>
          <span class="quick__body">
            <span class="quick__title">Submit Complaint</span><br>
            <span class="quick__desc">Report an issue or concern to your LGU.</span>
          </span>
          <svg class="icon quick__chevron"><use href="#i-chevron-right"/></svg>
        </a>
        <?php endif; ?>
        <a class="quick__card" href="#">
          <span class="bubble tone-amber"><svg class="icon"><use href="#i-search"/></svg></span>
          <span class="quick__body">
            <span class="quick__title">Track Complaint</span><br>
            <span class="quick__desc">Check the status of your submitted complaint.</span>
          </span>
          <svg class="icon quick__chevron"><use href="#i-chevron-right"/></svg>
        </a>
        <a class="quick__card" href="#">
          <span class="bubble tone-green"><svg class="icon"><use href="#i-file"/></svg></span>
          <span class="quick__body">
            <span class="quick__title">My Complaints</span><br>
            <span class="quick__desc">View your complaint history and updates.</span>
          </span>
          <svg class="icon quick__chevron"><use href="#i-chevron-right"/></svg>
        </a>
        <a class="quick__card" href="#">
          <span class="bubble tone-amber"><svg class="icon"><use href="#i-headset"/></svg></span>
          <span class="quick__body">
            <span class="quick__title">Contact Support</span><br>
            <span class="quick__desc">Get help and assistance from our team.</span>
          </span>
          <svg class="icon quick__chevron"><use href="#i-chevron-right"/></svg>
        </a>
        </div>
      </section>

      <div class="grid">
        <div class="grid__main">

          <section aria-labelledby="statsTitle">
            <h2 class="section-title" id="statsTitle">Complaint Statistics</h2>
            <div class="stats">
          <div class="stat">
            <span class="bubble tone-blue"><svg class="icon"><use href="#i-file"/></svg></span>
            <div>
              <p class="stat__value"><?php echo $totalComplaints; ?></p>
              <p class="stat__label">Total Complaints</p>
            </div>
          </div>
          <div class="stat">
            <span class="bubble tone-amber"><svg class="icon"><use href="#i-clock"/></svg></span>
            <div>
              <p class="stat__value"><?php echo $pendingCount; ?></p>
              <p class="stat__label">Pending</p>
            </div>
          </div>
          <div class="stat">
            <span class="bubble tone-blue"><svg class="icon"><use href="#i-gear"/></svg></span>
            <div>
              <p class="stat__value"><?php echo $inProgressCount; ?></p>
              <p class="stat__label">In Progress</p>
            </div>
          </div>
          <div class="stat">
            <span class="bubble tone-green"><svg class="icon"><use href="#i-check-circle"/></svg></span>
            <div>
              <p class="stat__value"><?php echo $resolvedCount; ?></p>
              <p class="stat__label">Resolved</p>
            </div>
          </div>
            </div>
          </section>

          <section class="panel" aria-labelledby="recentTitle">
            <div class="panel__head">
              <h2 class="panel__title" id="recentTitle">Recent Complaints</h2>
              <a class="link" href="#">View All <svg class="icon"><use href="#i-arrow-right"/></svg></a>
            </div>
            <div class="table-wrap">
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">Complaint ID</th>
                    <th scope="col">Title</th>
                    <th scope="col">Date Submitted</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
              <?php if (empty($recentComplaints)): ?>
                <tr><td colspan="5" style="text-align:center;">No complaints found.</td></tr>
              <?php else: ?>
                <?php foreach (array_slice($recentComplaints, 0, 5) as $comp): ?>
                <tr>
                    <td>CC-<?php echo str_pad($comp->getId(), 4, '0', STR_PAD_LEFT); ?></td>
                    <td><?php echo htmlspecialchars($comp->getTitle()); ?></td>
                    <td><?php echo date('M d, Y', strtotime($comp->getDateSubmitted())); ?></td>
                    <td>
                        <?php
                        $statusClass = 'pill--pending';
                        if ($comp->getStatus() === 'In Progress') $statusClass = 'pill--in-progress';
                        elseif ($comp->getStatus() === 'Resolved') $statusClass = 'pill--resolved';
                        ?>
                        <span class="pill <?php echo $statusClass; ?>"><?php echo $comp->getStatus(); ?></span>
                    </td>
                    <td><a class="btn-view" href="viewComplaintDetails.php?id=<?php echo $comp->getId(); ?>"><svg class="icon"><use href="#i-eye"/></svg> View</a></td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
                </tbody>
              </table>
            </div>
          </section>

          <section class="panel" aria-labelledby="annTitle">
            <div class="panel__head">
              <h2 class="panel__title panel__title--icon" id="annTitle">
                <svg class="icon"><use href="#i-megaphone"/></svg> Announcements &amp; Updates
              </h2>
              <a class="link" href="#">View All <svg class="icon"><use href="#i-arrow-right"/></svg></a>
            </div>
            <div class="ann-grid">
            <article class="ann tone-blue">
              <div class="ann__top">
                <span class="bubble"><svg class="icon"><use href="#i-info"/></svg></span>
                <div class="ann__text">
                  <h3 class="ann__title">LGU Service Advisory</h3>
                  <p class="ann__desc">Temporary suspension of office operations on August 30 for a city-wide event.</p>
                </div>
              </div>
              <div class="ann__foot">
                <span class="tag">LGU Announcement</span>
                <span class="ann__date">Aug 27, 2025</span>
              </div>
            </article>
            <article class="ann tone-amber">
              <div class="ann__top">
                <span class="bubble"><svg class="icon"><use href="#i-note"/></svg></span>
                <div class="ann__text">
                  <h3 class="ann__title">Community Notice</h3>
                  <p class="ann__desc">Clean-up drive this Saturday at Barangay San Jose. Everyone is invited.</p>
                </div>
              </div>
              <div class="ann__foot">
                <span class="tag">Community Notice</span>
                <span class="ann__date">Aug 25, 2025</span>
              </div>
            </article>
            <article class="ann tone-green">
              <div class="ann__top">
                <span class="bubble"><svg class="icon"><use href="#i-gear"/></svg></span>
                <div class="ann__text">
                  <h3 class="ann__title">System Update</h3>
                  <p class="ann__desc">New features now available on Citizen Connect. Check them out!</p>
                </div>
              </div>
              <div class="ann__foot">
                <span class="tag">System Update</span>
                <span class="ann__date">Aug 22, 2025</span>
              </div>
            </article>
            </div>
          </section>
        </div>

        <aside class="panel panel--side" aria-label="Complaint overview">
          <h2 class="panel__title">Complaint Status Overview</h2>
          <div class="overview">
            <?php
            $circumference = 377;
            $pendingArc    = $totalComplaints > 0 ? round(($pendingCount / $totalComplaints) * $circumference, 2) : 0;
            $inProgressArc = $totalComplaints > 0 ? round(($inProgressCount / $totalComplaints) * $circumference, 2) : 0;
            $resolvedArc   = $totalComplaints > 0 ? round(($resolvedCount / $totalComplaints) * $circumference, 2) : 0;
            ?>
            <svg class="donut" viewBox="0 0 160 160" role="img" aria-label="Total complaints by status">
              <circle cx="80" cy="80" r="60" fill="none" stroke="#fbb724" stroke-width="22" stroke-dasharray="<?php echo $pendingArc; ?> <?php echo $circumference; ?>" stroke-dashoffset="0" transform="rotate(-90 80 80)"/>
              <circle cx="80" cy="80" r="60" fill="none" stroke="#1f6feb" stroke-width="22" stroke-dasharray="<?php echo $inProgressArc; ?> <?php echo $circumference; ?>" stroke-dashoffset="<?php echo -$pendingArc; ?>" transform="rotate(-90 80 80)"/>
              <circle cx="80" cy="80" r="60" fill="none" stroke="#1fa855" stroke-width="22" stroke-dasharray="<?php echo $resolvedArc; ?> <?php echo $circumference; ?>" stroke-dashoffset="<?php echo -($pendingArc + $inProgressArc); ?>" transform="rotate(-90 80 80)"/>
              <text class="donut__total" x="80" y="82" text-anchor="middle"><?php echo $totalComplaints; ?></text>
              <text class="donut__label" x="80" y="97" text-anchor="middle">Total Complaints</text>
            </svg>
            <table class="legend">
              <tbody>
            <tr>
              <td><span class="legend__dot" style="background:#fbb724"></span>Pending</td>
              <td class="legend__count"><?php echo $pendingCount; ?></td>
              <td class="legend__pct"><?php echo ($totalComplaints > 0 ? round(($pendingCount/$totalComplaints)*100, 1) : 0); ?>%</td>
            </tr>
            <tr>
              <td><span class="legend__dot" style="background:#1f6feb"></span>In Progress</td>
              <td class="legend__count"><?php echo $inProgressCount; ?></td>
              <td class="legend__pct"><?php echo ($totalComplaints > 0 ? round(($inProgressCount/$totalComplaints)*100, 1) : 0); ?>%</td>
            </tr>
            <tr>
              <td><span class="legend__dot" style="background:#1fa855"></span>Resolved</td>
              <td class="legend__count"><?php echo $resolvedCount; ?></td>
              <td class="legend__pct"><?php echo ($totalComplaints > 0 ? round(($resolvedCount/$totalComplaints)*100, 1) : 0); ?>%</td>
            </tr>
              </tbody>
            </table>
          </div>

          <h2 class="panel__title panel__title--spaced">Complaint Activity</h2>
          <ol class="activity">
          <?php foreach (array_slice($recentComplaints, 0, 4) as $comp): ?>
          <li>
            <span class="bubble tone-blue"><svg class="icon"><use href="#i-gear"/></svg></span>
            <div>
              <p class="activity__msg">CC-<?php echo str_pad($comp->getId(), 4, '0', STR_PAD_LEFT); ?> was <?php echo strtolower($comp->getStatus()); ?></p>
              <p class="activity__time"><?php echo date('M d, Y • h:i A', strtotime($comp->getDateSubmitted())); ?></p>
            </div>
          </li>
          <?php endforeach; ?>
          </ol>
        </aside>
      </div>

    </main>
  </div>
</div>

</body>
</html>
