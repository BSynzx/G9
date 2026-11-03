<?php
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Complaint.php';

$auth = new Auth();
$user = $auth->currentUser();

if (!($user instanceof Admin) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/login.php");
    exit;
}

$complaintId = (int)$_POST['complaint_id'];
$staffId     = (int)$_POST['staff_id'];
$priority    = $_POST['priority'] ?? 'Normal';

$complaint = Complaint::find($complaintId);
if (!$complaint) {
    die("Complaint not found.");
}

$complaint->assign($staffId, $priority, (int)$user->getUserId());

echo "<script>alert('Complaint assigned successfully!'); window.location.href='../views/viewComplaintDetails.php?id=$complaintId';</script>";
