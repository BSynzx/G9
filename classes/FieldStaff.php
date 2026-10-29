<?php

require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Database.php';

class FieldStaff extends User
{
    public function getRole(): string      { return 'Field Staff'; }
    public function getTableName(): string { return 'Field_Staff'; }
    public function getIdColumn(): string  { return 'Staff_ID'; }

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
