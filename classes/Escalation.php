<?php

require_once __DIR__ . '/Database.php';

class Escalation
{
    private ?int $id;
    private int $complaintId;
    private int $adminId;
    private string $reason;
    private string $dateEscalated;
    private bool $resolved;

    public function __construct(
        int $complaintId,
        int $adminId,
        string $reason,
        ?int $id = null,
        string $dateEscalated = '',
        bool $resolved = false
    ) {
        $this->id            = $id;
        $this->complaintId   = $complaintId;
        $this->adminId       = $adminId;
        $this->reason        = $reason;
        $this->dateEscalated = $dateEscalated;
        $this->resolved      = $resolved;
    }

    public function getId(): ?int            { return $this->id; }
    public function getComplaintId(): int    { return $this->complaintId; }
    public function getAdminId(): int        { return $this->adminId; }
    public function getReason(): string      { return $this->reason; }
    public function getDateEscalated(): string { return $this->dateEscalated; }
    public function isResolved(): bool       { return $this->resolved; }

    public function save(): bool
    {
        $conn = Database::getInstance()->getConnection();

        $stmt = $conn->prepare(
            "INSERT INTO escalations (Complaint_ID, Admin_ID, Reason) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("iis", $this->complaintId, $this->adminId, $this->reason);
        $success = $stmt->execute();
        if ($success) {
            $this->id = $conn->insert_id;
        }
        $stmt->close();

        return $success;
    }
}
