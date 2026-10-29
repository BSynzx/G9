<?php
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Rating.php';
require_once __DIR__ . '/../classes/Notification.php';

$auth = new Auth();
$user = $auth->currentUser();

if ($user === null || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/login.php");
    exit;
}

$complaintId = (int)$_POST['complaint_id'];
$stars       = (int)$_POST['stars'];
$comment     = $_POST['comment'] ?? '';

$rating = new Rating($complaintId, (int)$user->getUserId(), $stars, $comment);
$rating->save();

$message = "New rating received for complaint CC-" . str_pad((string)$complaintId, 4, '0', STR_PAD_LEFT);
(new Notification(10000001, 'Admin', $message))->send();

echo "<script>alert('Thank you for your feedback!'); window.location.href='../views/viewComplaintDetails.php?id=$complaintId';</script>";
