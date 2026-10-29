<?php
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../classes/Barangay.php';
require_once __DIR__ . '/../classes/Notification.php';

$auth = new Auth();
$user = $auth->currentUser();

if ($user === null) {
    header("Location: login.php");
    exit;
}

if ($user->getTableName() !== 'citizen') {
    header("Location: dashboard.php");
    exit;
}

$userType = ($user instanceof Admin) ? 'Admin' : (($user instanceof FieldStaff) ? 'Staff' : 'Citizen');
$unreadCount = Notification::unreadCount((int)$user->getUserId(), $userType);

$categories = Category::all();
$barangays  = Barangay::all();

$fullName = $user->getFullName();
$role     = $user->getRole();

$isAdmin    = $user instanceof Admin;
$isStaff    = $user instanceof FieldStaff;
$badgeClass = $isAdmin ? 'pill--in-progress' : ($isStaff ? 'pill--pending' : 'pill--resolved');
$active     = 'report';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Citizen Connect – Submit Complaint</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=League+Spartan:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<style>
    body::before { display: none !important; }

    .field {
        padding: 0 24px !important;
        width: 100% !important;
        max-width: 800px !important;
        border: 1px solid var(--line) !important;
        background: #fff !important;
    }
    textarea.field {
        padding: 20px 24px !important;
        min-height: 250px !important;
        border-radius: 28px !important;
        resize: vertical;
        line-height: 1.6 !important;
        font-size: 1.1rem !important;
    }
    .form-group--description {
        margin-top: 30px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--navy);
    }
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
        <section class="panel">
            <h2 class="panel__title">Submit a New Complaint</h2>
            <p style="color: var(--muted); font-size: 0.9rem; margin-bottom: 24px;">Please provide as much detail as possible to help us address your concern effectively.</p>

            <form action="../actions/submitReport.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Subject / Title</label>
                    <input type="text" name="title" required class="field" placeholder="e.g., Broken street light in Brgy. San Jose">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; max-width: 800px;">
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category_id" required class="field" style="height: 56px; border-radius: 28px;">
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat->getId(); ?>"><?php echo htmlspecialchars($cat->getName()); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Barangay</label>
                        <select name="barangay_id" required class="field" style="height: 56px; border-radius: 28px;">
                            <option value="">Select Barangay</option>
                            <?php foreach ($barangays as $brgy): ?>
                                <option value="<?php echo $brgy->getId(); ?>"><?php echo htmlspecialchars($brgy->getName()); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Attach Photos</label>
                    <input type="file" name="photos[]" multiple class="field" accept="image/*" style="padding: 12px 24px !important;">
                </div>

                <div class="form-group form-group--description">
                    <label class="form-label">Description</label>
                    <textarea name="description" required class="field" placeholder="Please describe the issue in detail..."></textarea>
                </div>
                <div style="display: flex; gap: 12px; margin-top: 24px;">
                    <button type="submit" class="btn-cta" style="border:0; cursor:pointer;">
                        <svg class="icon"><use href="#i-pencil"/></svg>
                        Submit Report
                    </button>
                    <a href="dashboard.php" class="btn-view" style="text-decoration:none;">Cancel</a>
                </div>
            </form>
        </section>
    </main>
  </div>
</div>

</body>
</html>
