<?php

require_once __DIR__ . '/classes/Auth.php';

$auth = new Auth();

$target = $auth->currentUser() !== null ? 'views/dashboard.php' : 'views/login.php';

header("Location: " . $target);
exit;
