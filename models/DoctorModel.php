<?php

require_once __DIR__ . "/BaseModel.php";

class DoctorModel extends BaseModel
{
    public function findByUserId(int $userId): ?array
    {
        $result = $this->execute(
            "
            SELECT
                d.*,
                u.name,
                u.email,
                u.phone,
                s.name AS specialization
            FROM doctors d
            JOIN users u
                ON d.user_id=u.id
            JOIN specializations s
                ON d.specialization_id=s.id
            WHERE d.user_id=?
            ",
            "i",
            [$userId]
        );

        return $result->fetch_assoc() ?: null;
    }

    public function findById(int $id): ?array
    {
        $result = $this->execute(
            "
        SELECT *
        FROM doctors
        WHERE id=?
        ",
            "i",
            [$id]
        );

        return $result->fetch_assoc()
            ?: null;
    }

    public function getAll(): array
    {
        $result = $this->execute(
            "
            SELECT
                d.id,
                u.name
            FROM doctors d
            JOIN users u
                ON d.user_id=u.id
            ORDER BY u.name
            "
        );

        return $result->fetch_all(
            MYSQLI_ASSOC
        );
    }

    public function getAllPaginated(
        int $page
    ): array {

        $offset =
            max(0, $page - 1)
            * ITEMS_PER_PAGE;

        $result = $this->execute(
            "
            SELECT
                d.*,
                u.name,
                u.email,
                s.name AS specialization
            FROM doctors d
            JOIN users u
                ON d.user_id=u.id
            JOIN specializations s
                ON d.specialization_id=s.id
            ORDER BY d.id DESC
            LIMIT ?
            OFFSET ?
            ",
            "ii",
            [
                ITEMS_PER_PAGE,
                $offset
            ]
        );

        return $result->fetch_all(
            MYSQLI_ASSOC
        );
    }

    public function create(
        array $data
    ): int {

        $this->execute(
            "
            INSERT INTO doctors
            (
                user_id,
                specialization_id,
                bio,
                consultation_fee,
                available_days
            )
            VALUES (?,?,?,?,?)
            ",
            "iisss",
            [
                $data["user_id"],
                $data["specialization_id"],
                $data["bio"] ?? null,
                $data["consultation_fee"] ?? 0,
                $data["available_days"] ?? ""
            ]
        );

        return $this->db->lastInsertId();
    }

    public function update(
        int $doctorId,
        array $data
    ): bool {

        $result = $this->execute(
            "
            UPDATE doctors
            SET
                specialization_id=?,
                bio=?,
                consultation_fee=?,
                available_days=?,
                photo=?
            WHERE id=?
            ",
            "isdssi",
            [
                $data["specialization_id"],
                $data["bio"],
                $data["consultation_fee"],
                $data["available_days"],
                $data["photo"],
                $doctorId
            ]
        );

        return $result === true;
    }

    public function getAvailableDays(
        int $doctorId
    ): array {

        $result = $this->execute(
            "
            SELECT available_days
            FROM doctors
            WHERE id=?
            ",
            "i",
            [$doctorId]
        );

        $row =
            $result->fetch_assoc();

        if (!$row) {
            return [];
        }

        return array_map(
            "trim",
            explode(
                ",",
                $row["available_days"]
            )
        );
    }
    public function getAllDoctors(): array
    {
        $result = $this->execute(
            "
        SELECT
            d.*,
            u.name AS doctor_name,
            u.phone,
            s.name AS specialization_name
        FROM doctors d
        INNER JOIN users u
            ON d.user_id = u.id
        INNER JOIN specializations s
            ON d.specialization_id = s.id
        ORDER BY u.name
        "
        );

        return $result->fetch_all(
            MYSQLI_ASSOC
        );
    }
}
