<?php
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Escalation.php';
require_once __DIR__ . '/../classes/Notification.php';

$auth = new Auth();
$user = $auth->currentUser();

if (!($user instanceof Admin) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/login.php");
    exit;
}

$complaintId = (int)$_POST['complaint_id'];
$adminId     = (int)$user->getUserId();

$escalation = new Escalation($complaintId, $adminId, "Escalated for immediate review by Admin.");
$escalation->save();

$message = "Complaint CC-" . str_pad((string)$complaintId, 4, '0', STR_PAD_LEFT) . " has been escalated.";
(new Notification(20000001, 'Staff', $message))->send();

echo "<script>alert('Complaint escalated successfully!'); window.location.href='../views/viewComplaintDetails.php?id=$complaintId';</script>";
