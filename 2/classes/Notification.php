<?php

require_once __DIR__ . '/Database.php';

class Notification
{
    private ?int $id;
    private int $userId;
    private string $userType;
    private string $message;
    private string $channel;
    private bool $isRead;
    private string $dateSent;

    public function __construct(
        int $userId,
        string $userType,
        string $message,
        string $channel = 'In-App',
        ?int $id = null,
        bool $isRead = false,
        string $dateSent = ''
    ) {
        $this->id       = $id;
        $this->userId   = $userId;
        $this->userType = $userType;
        $this->message  = $message;
        $this->channel  = $channel;
        $this->isRead   = $isRead;
        $this->dateSent = $dateSent;
    }

    public function getId(): ?int           { return $this->id; }
    public function getUserId(): int        { return $this->userId; }
    public function getUserType(): string   { return $this->userType; }
    public function getMessage(): string    { return $this->message; }
    public function getChannel(): string    { return $this->channel; }
    public function isRead(): bool          { return $this->isRead; }
    public function getDateSent(): string   { return $this->dateSent; }

    public function send(): bool
    {
        $conn = Database::getInstance()->getConnection();

        $stmt = $conn->prepare(
            "INSERT INTO notifications (User_ID, User_Type, Message, Channel) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("isss", $this->userId, $this->userType, $this->message, $this->channel);
        $success = $stmt->execute();
        if ($success) {
            $this->id = $conn->insert_id;
        }
        $stmt->close();

        return $success;
    }

    public static function forUser(int $userId, string $userType): array
    {
        $conn = Database::getInstance()->getConnection();

        $stmt = $conn->prepare(
            "SELECT * FROM notifications WHERE User_ID = ? AND User_Type = ? ORDER BY Date_Sent DESC"
        );
        $stmt->bind_param("is", $userId, $userType);
        $stmt->execute();
        $result = $stmt->get_result();

        $notifications = [];
        while ($row = $result->fetch_assoc()) {
            $notifications[] = new Notification(
                (int) $row['User_ID'],
                (string) $row['User_Type'],
                (string) $row['Message'],
                (string) $row['Channel'],
                (int) $row['Notification_ID'],
                (bool) $row['Is_Read'],
                (string) $row['Date_Sent']
            );
        }
        $stmt->close();

        return $notifications;
    }

    public static function unreadCount(int $userId, string $userType): int
    {
        $conn = Database::getInstance()->getConnection();

        $stmt = $conn->prepare(
            "SELECT COUNT(*) AS total FROM notifications
             WHERE User_ID = ? AND User_Type = ? AND Is_Read = 0"
        );
        $stmt->bind_param("is", $userId, $userType);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return (int) $row['total'];
    }
}
