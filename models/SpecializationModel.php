<?php

require_once __DIR__ . "/BaseModel.php";

class SpecializationModel extends BaseModel
{
    public function getAll(): array
    {
        $result = $this->execute(
            "
            SELECT *
            FROM specializations
            ORDER BY name
            "
        );

        return $result->fetch_all(
            MYSQLI_ASSOC
        );
    }

    public function findById(
        int $id
    ): ?array {

        $result = $this->execute(
            "
            SELECT *
            FROM specializations
            WHERE id=?
            ",
            "i",
            [$id]
        );

        return $result->fetch_assoc()
            ?: null;
    }

    public function create(
        string $name
    ): int {

        $this->execute(
            "
            INSERT INTO specializations
            (name)
            VALUES (?)
            ",
            "s",
            [$name]
        );
        
        return $this->db->lastInsertId();
    }

    public function update(
        int $id,
        string $name
    ): bool {

        $result = $this->execute(
            "
            UPDATE specializations
            SET name=?
            WHERE id=?
            ",
            "si",
            [
                $name,
                $id
            ]
        );

        return $result === true;
    }

    public function delete(
        int $id
    ): bool {

        $result = $this->execute(
            "
            DELETE FROM specializations
            WHERE id=?
            ",
            "i",
            [$id]
        );

        return $result === true;
    }
}