<?php

require_once __DIR__ . '/Database.php';

class Barangay
{
    private ?int $id;
    private string $name;
    private ?string $district;
    private ?int $populationEstimate;

    public function __construct(
        string $name,
        ?string $district = null,
        ?int $populationEstimate = null,
        ?int $id = null
    ) {
        $this->id                 = $id;
        $this->name               = $name;
        $this->district           = $district;
        $this->populationEstimate = $populationEstimate;
    }

    public function getId(): ?int                  { return $this->id; }
    public function getName(): string              { return $this->name; }
    public function getDistrict(): ?string         { return $this->district; }
    public function getPopulationEstimate(): ?int  { return $this->populationEstimate; }

    public static function all(): array
    {
        $conn   = Database::getInstance()->getConnection();
        $result = $conn->query("SELECT * FROM barangays ORDER BY Barangay_Name ASC");

        $barangays = [];
        while ($row = $result->fetch_assoc()) {
            $barangays[] = new Barangay(
                (string) $row['Barangay_Name'],
                $row['District'],
                $row['Population_Estimate'] !== null ? (int) $row['Population_Estimate'] : null,
                (int) $row['Barangay_ID']
            );
        }

        return $barangays;
    }
}
