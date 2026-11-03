<?php

require_once __DIR__ . '/../classes/Auth.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../views/login.php");
    exit;
}

$email         = trim($_POST['email'] ?? '');
$plainPassword = $_POST['password'] ?? '';

if ($email === '' || $plainPassword === '') {
    die("Please enter your email and password. <a href='../views/login.php'>Go back</a>");
}

$auth = new Auth();
$user = $auth->attempt($email, $plainPassword);

if ($user !== null) {
    header("Location: ../views/dashboard.php");
    exit;
}

die("Invalid email or password. <a href='../views/login.php'>Go back</a>");
