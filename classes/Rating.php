<?php

require_once __DIR__ . '/Database.php';

class Rating
{
    private ?int $id;
    private int $complaintId;
    private int $citizenId;
    private ?int $staffId;
    private int $stars;
    private ?string $comment;
    private string $dateRated;

    public function __construct(
        int $complaintId,
        int $citizenId,
        int $stars,
        ?string $comment = null,
        ?int $staffId = null,
        ?int $id = null,
        string $dateRated = ''
    ) {
        $this->id          = $id;
        $this->complaintId = $complaintId;
        $this->citizenId   = $citizenId;
        $this->staffId     = $staffId;
        $this->stars       = $stars;
        $this->comment     = $comment;
        $this->dateRated   = $dateRated;
    }

    public function getId(): ?int          { return $this->id; }
    public function getComplaintId(): int  { return $this->complaintId; }
    public function getCitizenId(): int    { return $this->citizenId; }
    public function getStaffId(): ?int     { return $this->staffId; }
    public function getStars(): int        { return $this->stars; }
    public function getComment(): ?string  { return $this->comment; }
    public function getDateRated(): string { return $this->dateRated; }

    public function save(): bool
    {
        $conn = Database::getInstance()->getConnection();

        $stmt = $conn->prepare(
            "INSERT INTO ratings (Complaint_ID, Citizen_ID, Staff_ID, Stars, Comment) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "iiiis",
            $this->complaintId,
            $this->citizenId,
            $this->staffId,
            $this->stars,
            $this->comment
        );
        $success = $stmt->execute();
        if ($success) {
            $this->id = $conn->insert_id;
        }
        $stmt->close();

        return $success;
    }

    public static function forComplaint(int $complaintId): ?Rating
    {
        $conn = Database::getInstance()->getConnection();

        $stmt = $conn->prepare("SELECT * FROM ratings WHERE Complaint_ID = ?");
        $stmt->bind_param("i", $complaintId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) {
            return null;
        }

        return new Rating(
            (int) $row['Complaint_ID'],
            (int) $row['Citizen_ID'],
            (int) $row['Stars'],
            $row['Comment'],
            $row['Staff_ID'] !== null ? (int) $row['Staff_ID'] : null,
            (int) $row['Rating_ID'],
            (string) $row['Date_Rated']
        );
    }
}
