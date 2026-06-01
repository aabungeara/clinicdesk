<?php

require_once __DIR__ . "/../config/database.php";

class Database
{
    private static ?Database $instance = null;

    private mysqli $conn;

    private function __construct()
    {
        $this->conn = new mysqli(
            DB_HOST,
            DB_USER,
            DB_PASS,
            DB_NAME
        );

        if ($this->conn->connect_error) {
            throw new RuntimeException(
                "Database connection failed"
            );
        }

        $this->conn->set_charset("utf8mb4");
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function query(
        string $sql,
        string $types = "",
        array $params = []
    ) {
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        if ($types !== "" && !empty($params)) {
            $stmt->bind_param(
                $types,
                ...$params
            );
        }

        if (!$stmt->execute()) {
            return false;
        }

        $result = $stmt->get_result();

        if ($result instanceof mysqli_result) {
            return $result;
        }

        return true;
    }

    public function lastInsertId(): int
    {
        return $this->conn->insert_id;
    }
}