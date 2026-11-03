<?php

// User.php now also declares Admin and FieldStaff (single file, shared hierarchy).
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/User.php';

class Auth
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function attempt(string $email, string $plainPassword): ?User
    {
        $user = $this->findByEmail($email);

        if ($user === null || !$user->verifyPassword($plainPassword)) {
            return null;
        }

        if (!$user->isPasswordHashed()) {
            $user->setPassword($plainPassword);
            $user->updatePassword();
        }

        $this->login($user);

        return $user;
    }

    public function login(User $user): void
    {
        session_regenerate_id(true);
        $_SESSION['email'] = $user->getEmail();
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public function check(): bool
    {
        return isset($_SESSION['email']);
    }

    public function currentUser(): ?User
    {
        if (!$this->check()) {
            return null;
        }

        return $this->findByEmail($_SESSION['email']);
    }

    private function findByEmail(string $email): ?User
    {
        $tables = [
            ['citizen',     'Citizen_ID'],
            ['Admin',       'Admin_ID'],
            ['Field_Staff', 'Staff_ID'],
        ];

        $conn = Database::getInstance()->getConnection();

        foreach ($tables as [$table, $idColumn]) {
            $stmt = $conn->prepare(
                "SELECT `$idColumn` AS id, `First_Name`, `Middle_Name`, `Last_Name`,
                        `Address`, `Contact`, `Email`, `Password`
                 FROM `$table`
                 WHERE `Email` = ?"
            );
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($row !== null) {
                return $this->buildUser($table, $row);
            }
        }

        return null;
    }

    private function buildUser(string $table, array $row): User
    {
        $args = [
            (string) $row['id'],
            (string) $row['First_Name'],
            (string) $row['Middle_Name'],
            (string) $row['Last_Name'],
            (string) $row['Address'],
            (string) $row['Contact'],
            (string) $row['Email'],
        ];

        if ($table === 'Admin') {
            $user = new Admin(...$args);
        } elseif ($table === 'Field_Staff') {
            $user = new FieldStaff(...$args);
        } else {
            $user = new User(...$args);
        }

        $user->setPasswordHash((string) $row['Password']);

        return $user;
    }
}
