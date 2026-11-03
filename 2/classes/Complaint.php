<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Notification.php';

class Complaint
{
    private ?int $id;
    private int $citizenId;
    private string $title;
    private string $description;
    private string $status;
    private string $dateSubmitted;
    private ?int $categoryId;
    private ?int $barangayId;

    public function __construct(
        int $citizenId,
        string $title,
        string $description,
        ?int $id = null,
        string $status = 'Received',
        string $dateSubmitted = '',
        ?int $categoryId = null,
        ?int $barangayId = null
    ) {
        $this->id            = $id;
        $this->citizenId     = $citizenId;
        $this->title         = $title;
        $this->description   = $description;
        $this->status        = $status;
        $this->dateSubmitted = $dateSubmitted;
        $this->categoryId    = $categoryId;
        $this->barangayId    = $barangayId;
    }

    public function getId(): ?int              { return $this->id; }
    public function getCitizenId(): int        { return $this->citizenId; }
    public function getTitle(): string         { return $this->title; }
    public function getDescription(): string   { return $this->description; }
    public function getStatus(): string        { return $this->status; }
    public function getDateSubmitted(): string { return $this->dateSubmitted; }
    public function getCategoryId(): ?int      { return $this->categoryId; }
    public function getBarangayId(): ?int      { return $this->barangayId; }

    public function setTitle(string $title): void             { $this->title = $title; }
    public function setDescription(string $description): void { $this->description = $description; }
    public function setStatus(string $status): void           { $this->status = $status; }

    public function code(): string
    {
        return 'CC-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    public function save(): bool
    {
        $conn = Database::getInstance()->getConnection();

        $stmt = $conn->prepare(
            "INSERT INTO complaints (Citizen_ID, Title, Description, Category_ID, Barangay_ID)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "issii",
            $this->citizenId,
            $this->title,
            $this->description,
            $this->categoryId,
            $this->barangayId
        );
        $success = $stmt->execute();
        if ($success) {
            $this->id = $conn->insert_id;
        }
        $stmt->close();

        return $success;
    }

    public function updateStatus(string $status, string $notes, int $userId, string $userType): void
    {
        $conn = Database::getInstance()->getConnection();

        $stmt = $conn->prepare("UPDATE complaints SET Status = ? WHERE Complaint_ID = ?");
        $stmt->bind_param("si", $status, $this->id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare(
            "INSERT INTO status_updates (Complaint_ID, Status_Stage, Notes, Updated_By, User_Type)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("issis", $this->id, $status, $notes, $userId, $userType);
        $stmt->execute();
        $stmt->close();

        $this->status = $status;

        (new Notification(
            $this->citizenId,
            'Citizen',
            "Your complaint {$this->code()} status has been updated to $status."
        ))->send();
    }

    public function assign(int $staffId, string $priority, int $adminId): void
    {
        $conn = Database::getInstance()->getConnection();

        $stmt = $conn->prepare(
            "INSERT INTO assignments (Complaint_ID, Staff_ID, Priority) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE Staff_ID = VALUES(Staff_ID), Priority = VALUES(Priority)"
        );
        $stmt->bind_param("iis", $this->id, $staffId, $priority);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("UPDATE complaints SET Status = 'Assigned' WHERE Complaint_ID = ?");
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $stmt->close();

        $this->status = 'Assigned';

        $notes = "Complaint assigned to Staff ID $staffId with $priority priority.";
        $stmt  = $conn->prepare(
            "INSERT INTO status_updates (Complaint_ID, Status_Stage, Notes, Updated_By, User_Type)
             VALUES (?, 'Assigned', ?, ?, 'Admin')"
        );
        $stmt->bind_param("isi", $this->id, $notes, $adminId);
        $stmt->execute();
        $stmt->close();

        (new Notification($staffId, 'Staff', "A new complaint {$this->code()} has been assigned to you."))->send();
        (new Notification($this->citizenId, 'Citizen', "Your complaint {$this->code()} has been assigned to a field staff for action."))->send();
    }

    public function assignedTo(): ?string
    {
        $conn = Database::getInstance()->getConnection();

        $stmt = $conn->prepare(
            "SELECT fs.First_Name, fs.Last_Name
             FROM Field_Staff fs
             JOIN assignments a ON fs.Staff_ID = a.Staff_ID
             WHERE a.Complaint_ID = ?"
        );
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ? $row['First_Name'] . ' ' . $row['Last_Name'] : null;
    }

    public static function find(int $id): ?Complaint
    {
        $conn = Database::getInstance()->getConnection();

        $stmt = $conn->prepare("SELECT * FROM complaints WHERE Complaint_ID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ? self::fromRow($row) : null;
    }

    public static function forCitizen(int $citizenId): array
    {
        $conn = Database::getInstance()->getConnection();

        $stmt = $conn->prepare("SELECT * FROM complaints WHERE Citizen_ID = ? ORDER BY Date_Submitted DESC");
        $stmt->bind_param("i", $citizenId);
        $stmt->execute();
        $result = $stmt->get_result();

        $complaints = [];
        while ($row = $result->fetch_assoc()) {
            $complaints[] = self::fromRow($row);
        }
        $stmt->close();

        return $complaints;
    }

    public static function all(): array
    {
        $conn   = Database::getInstance()->getConnection();
        $result = $conn->query("SELECT * FROM complaints ORDER BY Date_Submitted DESC");

        $complaints = [];
        while ($row = $result->fetch_assoc()) {
            $complaints[] = self::fromRow($row);
        }

        return $complaints;
    }

    private static function fromRow(array $row): Complaint
    {
        return new Complaint(
            (int) $row['Citizen_ID'],
            (string) $row['Title'],
            (string) $row['Description'],
            (int) $row['Complaint_ID'],
            (string) $row['Status'],
            (string) $row['Date_Submitted'],
            $row['Category_ID'] !== null ? (int) $row['Category_ID'] : null,
            $row['Barangay_ID'] !== null ? (int) $row['Barangay_ID'] : null
        );
    }
}
