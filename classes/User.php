<?php

require_once __DIR__ . '/Person.php';
require_once __DIR__ . '/Database.php';

class User extends Person
{
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
        parent::__construct($firstName, $middleName, $lastName, $address, $contact, $email);

        $this->userId = $userId;
    }

    public function getRole(): string      { return 'Citizen'; }
    public function getTableName(): string { return 'citizen'; }
    public function getIdColumn(): string  { return 'Citizen_ID'; }

    public function getUserId(): string          { return $this->userId; }
    public function setUserId(string $id): void  { $this->userId = $id; }

    public function setPassword(string $plainPassword): void
    {
        $this->passwordHash = password_hash($plainPassword, PASSWORD_DEFAULT);
    }

    public function setPasswordHash(string $hash): void { $this->passwordHash = $hash; }
    public function getPasswordHash(): string           { return $this->passwordHash; }

    public function isPasswordHashed(): bool
    {
        return !empty(password_get_info($this->passwordHash)['algo']);
    }

    public function verifyPassword(string $plainPassword): bool
    {
        if ($this->isPasswordHashed()) {
            return password_verify($plainPassword, $this->passwordHash);
        }

        return hash_equals($this->passwordHash, $plainPassword);
    }

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

    public function updatePassword(): void
    {
        $conn = Database::getInstance()->getConnection();

        $stmt = $conn->prepare("UPDATE `{$this->getTableName()}` SET `Password` = ? WHERE `Email` = ?");
        $stmt->bind_param("ss", $this->passwordHash, $this->email);
        $stmt->execute();
        $stmt->close();
    }
}
