<?php
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Complaint.php';
require_once __DIR__ . '/../classes/Photo.php';

$auth = new Auth();
$user = $auth->currentUser();

if ($user === null || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/login.php");
    exit;
}

if ($user->getTableName() !== 'citizen') {
    header("Location: ../views/dashboard.php");
    exit;
}

$title       = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$categoryId  = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
$barangayId  = !empty($_POST['barangay_id']) ? (int)$_POST['barangay_id'] : null;

if (empty($title) || empty($description)) {
    echo "Please fill in all fields.";
    exit;
}

$complaint = new Complaint(
    (int)$user->getUserId(),
    $title,
    $description,
    null,
    'Received',
    '',
    $categoryId,
    $barangayId
);
$complaint->save();

if (!empty($_FILES['photos']['name'][0])) {
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    foreach ($_FILES['photos']['tmp_name'] as $key => $tmpName) {
        $fileName = time() . '_' . $_FILES['photos']['name'][$key];

        if (move_uploaded_file($tmpName, $uploadDir . $fileName)) {
            $photo = new Photo(
                $complaint->getId(),
                'uploads/' . $fileName,
                $_FILES['photos']['type'][$key],
                (int)$user->getUserId()
            );
            $photo->save();
        }
    }
}

echo "<script>alert('Report submitted successfully!'); window.location.href='../views/dashboard.php';</script>";
