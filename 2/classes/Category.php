<?php

require_once __DIR__ . '/Database.php';

class Category
{
    private ?int $id;
    private string $name;
    private ?string $description;

    public function __construct(string $name, ?string $description = null, ?int $id = null)
    {
        $this->id          = $id;
        $this->name        = $name;
        $this->description = $description;
    }

    public function getId(): ?int            { return $this->id; }
    public function getName(): string        { return $this->name; }
    public function getDescription(): ?string { return $this->description; }

    public static function all(): array
    {
        $conn   = Database::getInstance()->getConnection();
        $result = $conn->query("SELECT * FROM categories ORDER BY Category_Name ASC");

        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = new Category(
                (string) $row['Category_Name'],
                $row['Description'],
                (int) $row['Category_ID']
            );
        }

        return $categories;
    }
}
