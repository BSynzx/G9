<?php

require_once __DIR__ . '/Database.php';

class Photo
{
    private ?int $id;
    private int $complaintId;
    private string $photoUrl;
    private ?string $photoType;
    private string $dateUploaded;
    private ?int $uploadedBy;

    public function __construct(
        int $complaintId,
        string $photoUrl,
        ?string $photoType = null,
        ?int $uploadedBy = null,
        ?int $id = null,
        string $dateUploaded = ''
    ) {
        $this->id           = $id;
        $this->complaintId  = $complaintId;
        $this->photoUrl     = $photoUrl;
        $this->photoType    = $photoType;
        $this->uploadedBy   = $uploadedBy;
        $this->dateUploaded = $dateUploaded;
    }

    public function getId(): ?int           { return $this->id; }
    public function getComplaintId(): int   { return $this->complaintId; }
    public function getPhotoUrl(): string   { return $this->photoUrl; }
    public function getPhotoType(): ?string { return $this->photoType; }
    public function getUploadedBy(): ?int   { return $this->uploadedBy; }
    public function getDateUploaded(): string { return $this->dateUploaded; }

    public function save(): bool
    {
        $conn = Database::getInstance()->getConnection();

        $stmt = $conn->prepare(
            "INSERT INTO photos (Complaint_ID, Photo_URL, Photo_Type, Uploaded_By) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("issi", $this->complaintId, $this->photoUrl, $this->photoType, $this->uploadedBy);
        $success = $stmt->execute();
        if ($success) {
            $this->id = $conn->insert_id;
        }
        $stmt->close();

        return $success;
    }

    public static function forComplaint(int $complaintId): array
    {
        $conn = Database::getInstance()->getConnection();

        $stmt = $conn->prepare("SELECT * FROM photos WHERE Complaint_ID = ?");
        $stmt->bind_param("i", $complaintId);
        $stmt->execute();
        $result = $stmt->get_result();

        $photos = [];
        while ($row = $result->fetch_assoc()) {
            $photos[] = new Photo(
                (int) $row['Complaint_ID'],
                (string) $row['Photo_URL'],
                $row['Photo_Type'],
                $row['Uploaded_By'] !== null ? (int) $row['Uploaded_By'] : null,
                (int) $row['Photo_ID'],
                (string) $row['Date_Uploaded']
            );
        }
        $stmt->close();

        return $photos;
    }
}
