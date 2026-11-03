<?php

// User.php - all user types in one file: User, Admin, FieldStaff.
// OOP: inheritance (extends), abstraction, encapsulation, polymorphism.

require_once __DIR__ . '/Person.php';
require_once __DIR__ . '/Database.php';

// User = a regular citizen (base account type).
class User extends Person
{
    // Encapsulation: private fields, accessed via getters/setters.
    private string $userId;
    private string $passwordHash = '';

    public function __construct(
        string $userId,
        string $firstName,
        string $middleName,
        string $lastName,
        string $address,
        string $contact,
        string $email
    ) {
        // Pass shared identity fields up to the Person parent.
        parent::__construct($firstName, $middleName, $lastName, $address, $contact, $email);

        $this->userId = $userId;
    }

    // Polymorphism: overridden by each subclass.
    public function getRole(): string      { return 'Citizen'; }
    public function getTableName(): string { return 'citizen'; }
    public function getIdColumn(): string  { return 'Citizen_ID'; }

    public function getUserId(): string          { return $this->userId; }
    public function setUserId(string $id): void  { $this->userId = $id; }

    // Hash a plain-text password before storing it.
    public function setPassword(string $plainPassword): void
    {
        $this->passwordHash = password_hash($plainPassword, PASSWORD_DEFAULT);
    }

    public function setPasswordHash(string $hash): void { $this->passwordHash = $hash; }
    public function getPasswordHash(): string           { return $this->passwordHash; }

    // True if the stored value is already a hash.
    public function isPasswordHashed(): bool
    {
        return !empty(password_get_info($this->passwordHash)['algo']);
    }

    // Check a login attempt against the stored hash (or plain text).
    public function verifyPassword(string $plainPassword): bool
    {
        if ($this->isPasswordHashed()) {
            return password_verify($plainPassword, $this->passwordHash);
        }

        return hash_equals($this->passwordHash, $plainPassword);
    }

    // Insert this user; table/id come from the overridden methods (polymorphism).
    public function save(): void
    {
        $conn = Database::getInstance()->getConnection();

        $sql = "INSERT INTO `{$this->getTableName()}`
                (`{$this->getIdColumn()}`, `First_Name`, `Middle_Name`, `Last_Name`,
                 `Address`, `Contact`, `Email`, `Password`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "isssssss",
            $this->userId,
            $this->firstName,
            $this->middleName,
            $this->lastName,
            $this->address,
            $this->contact,
            $this->email,
            $this->passwordHash
        );
        $stmt->execute();
        $stmt->close();
    }

    // Update the password for this email.
    public function updatePassword(): void
    {
        $conn = Database::getInstance()->getConnection();

        $stmt = $conn->prepare("UPDATE `{$this->getTableName()}` SET `Password` = ? WHERE `Email` = ?");
        $stmt->bind_param("ss", $this->passwordHash, $this->email);
        $stmt->execute();
        $stmt->close();
    }
}

// Admin: inherits from User, overrides role/table/id.
class Admin extends User
{
    public function getRole(): string      { return 'Administrator'; }
    public function getTableName(): string { return 'Admin'; }
    public function getIdColumn(): string  { return 'Admin_ID'; }
}

// FieldStaff: inherits from User, overrides role/table/id.
class FieldStaff extends User
{
    public function getRole(): string      { return 'Field Staff'; }
    public function getTableName(): string { return 'Field_Staff'; }
    public function getIdColumn(): string  { return 'Staff_ID'; }

    // Load all field staff from the database.
    public static function all(): array
    {
        $conn   = Database::getInstance()->getConnection();
        $result = $conn->query("SELECT * FROM Field_Staff");

        $staffMembers = [];
        while ($row = $result->fetch_assoc()) {
            $staff = new FieldStaff(
                (string) $row['Staff_ID'],
                (string) $row['First_Name'],
                (string) $row['Middle_Name'],
                (string) $row['Last_Name'],
                (string) $row['Address'],
                (string) $row['Contact'],
                (string) $row['Email']
            );
            $staff->setPasswordHash((string) $row['Password']);
            $staffMembers[] = $staff;
        }

        return $staffMembers;
    }
}
