<?php
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Complaint.php';

$auth = new Auth();
$user = $auth->currentUser();

if ($user === null || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/login.php");
    exit;
}

$complaintId = (int)$_POST['complaint_id'];
$newStatus   = $_POST['status'];
$notes       = $_POST['notes'] ?? '';

$complaint = Complaint::find($complaintId);
if (!$complaint) {
    die("Complaint not found.");
}

$userType = ($user instanceof Admin) ? 'Admin' : 'Staff';

$complaint->updateStatus($newStatus, $notes, (int)$user->getUserId(), $userType);

echo "<script>alert('Status updated successfully!'); window.location.href='../views/viewComplaintDetails.php?id=$complaintId';</script>";
