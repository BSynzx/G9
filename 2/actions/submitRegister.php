<?php

require_once __DIR__ . '/../classes/User.php';

try {
    $user = new User(
        (string) rand(10000000, 99999999),
        $_POST['firstName'] ?? '',
        $_POST['middleName'] ?? '',
        $_POST['lastName'] ?? '',
        $_POST['address'] ?? '',
        $_POST['contact'] ?? '',
        $_POST['email'] ?? ''
    );

    $user->setPassword($_POST['password'] ?? '');
    $user->save();

    echo "New record created successfully";
} catch (Throwable $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
}
