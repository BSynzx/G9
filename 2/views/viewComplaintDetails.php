<?php
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Complaint.php';
require_once __DIR__ . '/../classes/Photo.php';
require_once __DIR__ . '/../classes/Rating.php';
// FieldStaff is now declared inside User.php (loaded via Auth.php above).
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
$isAdmin  = $user instanceof Admin;
$isStaff  = $user instanceof FieldStaff;
$badgeClass = $isAdmin ? 'pill--in-progress' : ($isStaff ? 'pill--pending' : 'pill--resolved');
$active   = 'details';

$complaintId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$complaint = Complaint::find($complaintId);

if (!$complaint) {
    die("Complaint not found.");
}

$photos       = Photo::forComplaint($complaintId);
$rating       = Rating::forComplaint($complaintId);
$allStaff     = $isAdmin ? FieldStaff::all() : [];
$currentStaff = $complaint->assignedTo();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Citizen Connect – Complaint Details</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=League+Spartan:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<style>
    .detail-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }
    .photo-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 12px; margin-top: 20px; }
    .photo-item img { width: 100%; height: 150px; object-fit: cover; border-radius: 12px; cursor: pointer; }
    .info-group { margin-bottom: 20px; }
    .info-label { font-size: 0.85rem; color: var(--muted); margin-bottom: 4px; }
    .info-value { font-weight: 500; color: var(--navy); }
</style>
</head>
<body>

<?php require_once __DIR__ . '/../includes/icons.php'; ?>

<input class="nav-toggle" type="checkbox" id="navToggle" aria-label="Toggle menu">

<div class="app">

<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

  <div class="app__main">

<?php require_once __DIR__ . '/../includes/topbar.php'; ?>

    <main class="content">
        <div class="detail-grid">
            <div class="grid__main">
                <section class="panel">
                    <div class="panel__head">
                        <h2 class="panel__title"><?php echo htmlspecialchars($complaint->getTitle()); ?></h2>
                        <span class="pill <?php echo ($complaint->getStatus() === 'Resolved' ? 'pill--resolved' : ($complaint->getStatus() === 'In Progress' ? 'pill--in-progress' : 'pill--pending')); ?>">
                            <?php echo $complaint->getStatus(); ?>
                        </span>
                    </div>
                    <p style="margin-top: 16px; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($complaint->getDescription())); ?></p>

                    <?php if (!empty($photos)): ?>
                        <h3 style="margin-top: 30px; font-size: 1.1rem;">Attached Photos</h3>
                        <div class="photo-gallery">
                            <?php foreach ($photos as $photo): ?>
                                <div class="photo-item">
                                    <img src="../<?php echo htmlspecialchars($photo->getPhotoUrl()); ?>" alt="Complaint Photo" onclick="window.open(this.src)">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

                <?php if ($complaint->getStatus() === 'Resolved' && !$rating && !($isAdmin || $isStaff)): ?>
                    <section class="panel" style="margin-top: 24px;">
                        <h2 class="panel__title">Rate the Resolution</h2>
                        <form action="../actions/submitRating.php" method="POST" style="margin-top: 16px;">
                            <input type="hidden" name="complaint_id" value="<?php echo $complaintId; ?>">
                            <div class="form-group">
                                <label class="form-label">Rating</label>
                                <div style="display: flex; gap: 8px;">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <label style="cursor: pointer;">
                                            <input type="radio" name="stars" value="<?php echo $i; ?>" required style="display:none;">
                                            <svg class="icon" style="width:32px; height:32px; color: #ddd;" onmouseover="this.style.color='#fbb724'" onmouseout="if(!this.previousElementSibling.checked) this.style.color='#ddd'" onclick="this.style.color='#fbb724'">
                                                <use href="#i-star"/>
                                            </svg>
                                        </label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: 16px;">
                                <label class="form-label">Comment (Optional)</label>
                                <textarea name="comment" class="field" style="min-height: 100px;"></textarea>
                            </div>
                            <button type="submit" class="btn-cta" style="margin-top: 16px; border:0; cursor:pointer;">Submit Rating</button>
                        </form>
                    </section>
                <?php elseif ($rating): ?>
                    <section class="panel" style="margin-top: 24px;">
                        <h2 class="panel__title">Citizen Rating</h2>
                        <div style="margin-top: 12px; display: flex; align-items: center; gap: 8px;">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <svg class="icon" style="color: <?php echo ($i <= $rating->getStars() ? '#fbb724' : '#ddd'); ?>;"><use href="#i-star"/></svg>
                            <?php endfor; ?>
                        </div>
                        <?php if ($rating->getComment()): ?>
                            <p style="margin-top: 8px; color: var(--muted);"><?php echo htmlspecialchars($rating->getComment()); ?></p>
                        <?php endif; ?>
                    </section>
                <?php endif; ?>
            </div>

            <aside>
                <section class="panel">
                    <h2 class="panel__title">Details</h2>
                    <div class="info-group" style="margin-top: 20px;">
                        <p class="info-label">Complaint ID</p>
                        <p class="info-value">CC-<?php echo str_pad($complaint->getId(), 4, '0', STR_PAD_LEFT); ?></p>
                    </div>
                    <div class="info-group">
                        <p class="info-label">Date Submitted</p>
                        <p class="info-value"><?php echo date('M d, Y', strtotime($complaint->getDateSubmitted())); ?></p>
                    </div>
                    <div class="info-group">
                        <p class="info-label">Category</p>
                        <p class="info-value"><?php

                            echo "Infrastructure";
                        ?></p>
                    </div>
                    <div class="info-group">
                        <p class="info-label">Barangay</p>
                        <p class="info-value"><?php

                            echo "Barangay 1";
                        ?></p>
                    </div>
                    <div class="info-group">
                        <p class="info-label">Assigned To</p>
                        <p class="info-value"><?php echo $currentStaff ?: 'Unassigned'; ?></p>
                    </div>
                </section>

                <?php if ($isAdmin || $isStaff): ?>
                    <section class="panel" style="margin-top: 24px;">
                        <h2 class="panel__title">Manage Complaint</h2>
                        <form action="../actions/updateStatus.php" method="POST" style="margin-top: 16px;">
                            <input type="hidden" name="complaint_id" value="<?php echo $complaintId; ?>">
                            <div class="form-group">
                                <label class="form-label">Update Status</label>
                                <select name="status" class="field" style="height: 44px; border-radius: 22px;">
                                    <option value="Received" <?php if($complaint->getStatus()=='Received') echo 'selected'; ?>>Received</option>
                                    <option value="Verified" <?php if($complaint->getStatus()=='Verified') echo 'selected'; ?>>Verified</option>
                                    <option value="Assigned" <?php if($complaint->getStatus()=='Assigned') echo 'selected'; ?>>Assigned</option>
                                    <option value="In Progress" <?php if($complaint->getStatus()=='In Progress') echo 'selected'; ?>>In Progress</option>
                                    <option value="Resolved" <?php if($complaint->getStatus()=='Resolved') echo 'selected'; ?>>Resolved</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: 12px;">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="field" style="min-height: 80px;"></textarea>
                            </div>
                            <button type="submit" class="btn-cta" style="margin-top: 16px; width: 100%; border:0; cursor:pointer;">Update Status</button>
                        </form>

                        <?php if ($isAdmin): ?>
                            <hr style="margin: 20px 0; border: 0; border-top: 1px solid var(--line);">
                            <form action="../actions/assignComplaint.php" method="POST">
                                <input type="hidden" name="complaint_id" value="<?php echo $complaintId; ?>">
                                <div class="form-group">
                                    <label class="form-label">Assign to Staff</label>
                                    <select name="staff_id" class="field" style="height: 44px; border-radius: 22px;" required>
                                        <option value="">Select Staff</option>
                                        <?php foreach ($allStaff as $staff): ?>
                                            <option value="<?php echo $staff->getUserId(); ?>"><?php echo htmlspecialchars($staff->getFullName()); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-top: 12px;">
                                    <label class="form-label">Priority</label>
                                    <select name="priority" class="field" style="height: 44px; border-radius: 22px;">
                                        <option value="Normal">Normal</option>
                                        <option value="High">High</option>
                                        <option value="Urgent">Urgent</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn-cta" style="margin-top: 16px; width: 100%; background: var(--blue); color: #fff; border:0; cursor:pointer;">Assign Staff</button>
                            </form>

                            <hr style="margin: 20px 0; border: 0; border-top: 1px solid var(--line);">
                            <form action="../actions/escalateComplaint.php" method="POST">
                                <input type="hidden" name="complaint_id" value="<?php echo $complaintId; ?>">
                                <button type="submit" class="btn-view" style="width: 100%; color: #d93025; border-color: #d93025;">Escalate to Mayor</button>
                            </form>
                        <?php endif; ?>
                    </section>
                <?php endif; ?>
            </aside>
        </div>
    </main>
  </div>
</div>
</body>
</html>
