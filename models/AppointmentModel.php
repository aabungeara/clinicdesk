<?php

require_once __DIR__ . "/BaseModel.php";

class AppointmentModel extends BaseModel
{
    public function book(array $data): bool
    {
        try {

            $this->execute(
                "
                INSERT INTO appointments
                (
                    patient_id,
                    doctor_id,
                    appt_date,
                    appt_time,
                    reason
                )
                VALUES (?,?,?,?,?)
                ",
                "iisss",
                [
                    $data["patient_id"],
                    $data["doctor_id"],
                    $data["appt_date"],
                    $data["appt_time"],
                    $data["reason"] ?? null
                ]
            );

            return true;
        } catch (Throwable $e) {

            return false;
        }
    }

    public function hasConflict(
        int $doctorId,
        string $date,
        string $time
    ): bool {

        $result = $this->execute(
            "
            SELECT id
            FROM appointments
            WHERE doctor_id=?
            AND appt_date=?
            AND appt_time=?
            ",
            "iss",
            [
                $doctorId,
                $date,
                $time
            ]
        );

        return $result->num_rows > 0;
    }

    private function buildFilters(
        array $filters,
        array &$params,
        string &$types
    ): array {

        $conditions = [];

        if (!empty($filters["doctor_id"])) {

            $conditions[] = "doctor_id=?";

            $types .= "i";

            $params[] = $filters["doctor_id"];
        }

        if (!empty($filters["status"])) {

            $conditions[] = "status=?";

            $types .= "s";

            $params[] = $filters["status"];
        }

        if (!empty($filters["date_from"])) {

            $conditions[] = "appt_date>=?";

            $types .= "s";

            $params[] = $filters["date_from"];
        }

        if (!empty($filters["date_to"])) {

            $conditions[] = "appt_date<=?";

            $types .= "s";

            $params[] = $filters["date_to"];
        }

        return $conditions;
    }
    public function getByPatient(
        int $patientId,
        int $page,
        array $filters
    ): array {

        $conditions = ["patient_id=?"];

        $params = [$patientId];

        $types = "i";

        $extra =
            $this->buildFilters(
                $filters,
                $params,
                $types
            );

        $conditions =
            array_merge(
                $conditions,
                $extra
            );

        $where =
            "WHERE "
            . implode(
                " AND ",
                $conditions
            );

        $offset =
            ($page - 1)
            * ITEMS_PER_PAGE;

        $types .= "ii";

        $params[] =
            ITEMS_PER_PAGE;

        $params[] =
            $offset;

        $result =
            $this->execute(
                "
            SELECT
                a.*,
                du.name AS doctor_name,
                s.name AS specialization_name
            FROM appointments a

            JOIN doctors d
                ON a.doctor_id = d.id

            JOIN users du
                ON d.user_id = du.id

            JOIN specializations s
                ON d.specialization_id = s.id

            $where

            ORDER BY
                a.appt_date DESC,
                a.appt_time DESC

            LIMIT ?
            OFFSET ?
            ",
                $types,
                $params
            );

        return $result->fetch_all(
            MYSQLI_ASSOC
        );
    }

    public function getByDoctor(
        int $doctorId,
        int $page,
        array $filters
    ): array {

        $filters["doctor_id"] =
            $doctorId;

        return $this->getAll(
            $page,
            $filters
        );
    }

    public function getAll(
        int $page,
        array $filters
    ): array {

        $params = [];
        $types = "";

        $conditions =
            $this->buildFilters(
                $filters,
                $params,
                $types
            );

        $where = "";

        if (!empty($conditions)) {

            $where =
                "WHERE "
                . implode(
                    " AND ",
                    $conditions
                );
        }

        $offset =
            ($page - 1)
            * ITEMS_PER_PAGE;

        $types .= "ii";

        $params[] =
            ITEMS_PER_PAGE;

        $params[] =
            $offset;

        $result =
            $this->execute(
                "
            SELECT
                a.*,
                p.name AS patient_name,
                du.name AS doctor_name
            FROM appointments a

            JOIN users p
                ON a.patient_id=p.id

            JOIN doctors d
                ON a.doctor_id=d.id

            JOIN users du
                ON d.user_id=du.id

            $where

            ORDER BY a.appt_date DESC

            LIMIT ?
            OFFSET ?
            ",
                $types,
                $params
            );

        return $result->fetch_all(
            MYSQLI_ASSOC
        );
    }

    public function countFiltered(
        string $scope,
        int $scopeId,
        array $filters
    ): int {

        $conditions = [];

        $params = [];

        $types = "";

        if ($scope === "patient") {

            $conditions[] =
                "patient_id=?";

            $params[] =
                $scopeId;

            $types .= "i";
        }

        if ($scope === "doctor") {

            $conditions[] =
                "doctor_id=?";

            $params[] =
                $scopeId;

            $types .= "i";
        }

        $extra =
            $this->buildFilters(
                $filters,
                $params,
                $types
            );

        $conditions =
            array_merge(
                $conditions,
                $extra
            );

        $where = "";

        if (!empty($conditions)) {

            $where =
                "WHERE "
                . implode(
                    " AND ",
                    $conditions
                );
        }

        $result =
            $this->execute(
                "
            SELECT COUNT(*) AS total
            FROM appointments
            $where
            ",
                $types,
                $params
            );

        return (int)
        $result
            ->fetch_assoc()["total"];
    }

    public function updateStatus(
        int $id,
        string $status,
        string $notes = ""
    ): bool {

        $result =
            $this->execute(
                "
                UPDATE appointments
                SET
                status=?,
                doctor_notes=?
                WHERE id=?
                ",
                "ssi",
                [
                    $status,
                    $notes,
                    $id
                ]
            );

        return $result === true;
    }

    public function findById(
        int $id
    ): ?array {

        $result =
            $this->execute(
                "
                SELECT

                a.*,

                p.name patient_name,

                du.name doctor_name

                FROM appointments a

                JOIN users p
                ON a.patient_id=p.id

                JOIN doctors d
                ON a.doctor_id=d.id

                JOIN users du
                ON d.user_id=du.id

                WHERE a.id=?

                ",
                "i",
                [$id]
            );

        return
            $result
            ->fetch_assoc()
            ?: null;
    }


    public function create(
        array $data
    ): bool {

        $result =
            $this->execute(
                "
            INSERT INTO appointments
            (
                patient_id,
                doctor_id,
                appt_date,
                appt_time,
                reason,
                status
            )
            VALUES
            (
                ?,?,?,?,?,
                'pending'
            )
            ",
                "iisss",
                [
                    $data["patient_id"],
                    $data["doctor_id"],
                    $data["appt_date"],
                    $data["appt_time"],
                    $data["reason"]
                ]
            );

        return $result === true;
    }

    public function cancel(
        int $id
    ): bool {

        $result =
            $this->execute(
                "
            UPDATE appointments
            SET status='cancelled'
            WHERE id=?
            ",
                "i",
                [$id]
            );

        return $result === true;
    }

    public function canAddPrescription(
    int $appointmentId,
    int $doctorUserId
): bool {

    $result =
        $this->execute(
            "
            SELECT a.id
            FROM appointments a

            JOIN doctors d
                ON a.doctor_id=d.id

            WHERE
                a.id=?
            AND d.user_id=?
            AND a.status='completed'
            ",
            "ii",
            [
                $appointmentId,
                $doctorUserId
            ]
        );

    return $result->num_rows > 0;
}
}
