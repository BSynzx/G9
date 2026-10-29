<?php

class Database
{
    private static ?Database $instance = null;

    private mysqli $connection;

    private string $servername = "localhost";
    private string $username   = "root";
    private string $password   = "";
    private string $dbname     = "citizen_connect";

    private function __construct()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $this->connection = new mysqli(
            $this->servername,
            $this->username,
            $this->password,
            $this->dbname
        );
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function getConnection(): mysqli
    {
        return $this->connection;
    }
}
