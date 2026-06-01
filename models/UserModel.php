<?php

require_once __DIR__ . "/BaseModel.php";

class UserModel extends BaseModel
{
    public function findById(int $id): ?array
    {
        $result = $this->execute(
            "SELECT * FROM users WHERE id=?",
            "i",
            [$id]
        );

        return $result->fetch_assoc() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $result = $this->execute(
            "SELECT * FROM users WHERE email=?",
            "s",
            [$email]
        );

        return $result->fetch_assoc() ?: null;
    }

    public function create(array $data): int
    {
        $this->execute(
            "INSERT INTO users
            (name,email,password,role,phone,avatar)
            VALUES (?,?,?,?,?,?)",
            "ssssss",
            [
                $data["name"],
                $data["email"],
                $data["password"],
                $data["role"],
                $data["phone"] ?? null,
                $data["avatar"] ?? null
            ]
        );

        return $this->db->lastInsertId();
    }

    public function update(
        int $id,
        array $data
    ): bool {
        $result = $this->execute(
            "UPDATE users
             SET name=?,
                 phone=?,
                 avatar=?
             WHERE id=?",
            "sssi",
            [
                $data["name"],
                $data["phone"],
                $data["avatar"],
                $id
            ]
        );

        return $result === true;
    }

    public function updatePassword(
        int $id,
        string $newHash
    ): bool {
        $result = $this->execute(
            "UPDATE users
             SET password=?
             WHERE id=?",
            "si",
            [
                $newHash,
                $id
            ]
        );

        return $result === true;
    }

    public function getAllPaginated(
        int $page,
        string $role = ""
    ): array {

        $offset =
            max(0, $page - 1)
            * ITEMS_PER_PAGE;

        if ($role !== "") {

            $result = $this->execute(
                "SELECT *
                 FROM users
                 WHERE role=?
                 LIMIT ?
                 OFFSET ?",
                "sii",
                [
                    $role,
                    ITEMS_PER_PAGE,
                    $offset
                ]
            );

        } else {

            $result = $this->execute(
                "SELECT *
                 FROM users
                 LIMIT ?
                 OFFSET ?",
                "ii",
                [
                    ITEMS_PER_PAGE,
                    $offset
                ]
            );
        }

        return $result->fetch_all(
            MYSQLI_ASSOC
        );
    }

    public function countAll(
        string $role = ""
    ): int {

        if ($role !== "") {

            $result = $this->execute(
                "SELECT COUNT(*) total
                 FROM users
                 WHERE role=?",
                "s",
                [$role]
            );

        } else {

            $result = $this->execute(
                "SELECT COUNT(*) total
                 FROM users"
            );
        }

        return (int)(
            $result->fetch_assoc()
        )["total"];
    }

    public function toggleActive(
        int $id
    ): bool {

        $result = $this->execute(
            "UPDATE users
             SET is_active=
             IF(is_active=1,0,1)
             WHERE id=?",
            "i",
            [$id]
        );

        return $result === true;
    }
}