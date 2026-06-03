<?php

require_once __DIR__ . "/../core/Database.php";

abstract class BaseModel
{
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    protected function execute(
        string $sql,
        string $types = "",
        array $params = []
    ) {
        try {

            return $this->db->query(
                $sql,
                $types,
                $params
            );

        } catch (Throwable $e) {
              die($e->getMessage());
        }
    }
}