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
                s.name AS specialization_name,
                IF(pr.id IS NOT NULL, 1, 0) AS has_prescription
            FROM appointments a

            JOIN doctors d
                ON a.doctor_id = d.id

            JOIN users du
                ON d.user_id = du.id

            JOIN specializations s
                ON d.specialization_id = s.id
                LEFT JOIN prescriptions pr 
                ON a.id = pr.appointment_id

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

        $conditions = ["a.doctor_id=?"];

        $params = [$doctorId];

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
            p.name AS patient_name,
            pr.id AS prescription_id

            FROM appointments a

            JOIN users p
            ON a.patient_id = p.id

            LEFT JOIN prescriptions pr
            ON pr.appointment_id = a.id
            
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

    public function prescriptionExists(
        int $appointmentId
    ): bool {

        $result =
            $this->execute(
                "
            SELECT id
            FROM prescriptions
            WHERE appointment_id=?
            ",
                "i",
                [$appointmentId]
            );

        return
            $result->num_rows > 0;
    }

    public function getTodayAppointments(
        int $doctorId
    ): array {

        $today = date("Y-m-d");

        $result = $this->execute(
            "
            SELECT
                a.*,
                u.name AS patient_name,
                pr.id AS prescription_id 
            FROM appointments a

            JOIN users u
                ON a.patient_id=u.id

            
            LEFT JOIN prescriptions pr 
                ON a.id = pr.appointment_id

            WHERE
                a.doctor_id=?
            AND
                a.appt_date=?

            ORDER BY
                a.appt_time ASC
            ",
            "is",
            [
                $doctorId,
                $today
            ]
        );

        return $result->fetch_all(
            MYSQLI_ASSOC
        );
    }

    public function getTodayByDoctor(
        int $doctorId
    ): array {

        $result = $this->execute(
            "
            SELECT
                a.*,
                u.name AS patient_name,
                pr.id AS prescription_id 
            FROM appointments a

            JOIN users u
                ON a.patient_id=u.id

            
            LEFT JOIN prescriptions pr 
                ON a.id = pr.appointment_id

            WHERE a.doctor_id=?
            AND a.appt_date=CURDATE()

            ORDER BY a.appt_time
            ",
            "i",
            [$doctorId]
        );

        return $result->fetch_all(
            MYSQLI_ASSOC
        );
    }

    public function confirm(
        int $id
    ): bool {

        return $this->execute(
            "
        UPDATE appointments
        SET status='confirmed'
        WHERE id=?
        ",
            "i",
            [$id]
        );
    }

    public function complete(
        int $id
    ): bool {

        return $this->execute(
            "
        UPDATE appointments
        SET status='completed'
        WHERE id=?
        ",
            "i",
            [$id]
        );
    }

    public function verifyOwnership(int $appointmentId, int $patientId): bool
    {
        $result = $this->execute(
            "SELECT id FROM appointments WHERE id = ? AND patient_id = ?",
            "ii",
            [$appointmentId, $patientId]
        );
        return $result && $result->num_rows > 0;
    }

    public function getUsersCountByRole(): array
    {
        $query = $this->execute("SELECT role, COUNT(*) as total FROM users GROUP BY role");
        return $query ? $query->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getTodayAppointmentsCount(): int
    {
        $query = $this->execute("SELECT COUNT(*) as total FROM appointments WHERE appt_date = CURDATE()");
        return $query ? (int)$query->fetch_assoc()["total"] : 0;
    }

    public function getWeekStatsByStatus(): array
    {
        $query = $this->execute("SELECT status, COUNT(*) as total FROM appointments WHERE WEEK(appt_date) = WEEK(NOW()) AND YEAR(appt_date) = YEAR(NOW()) GROUP BY status");
        return $query ? $query->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getRecentAppointments(int $limit = 5): array
    {
        $query = $this->execute("
        SELECT a.*, p.name AS patient_name, du.name AS doctor_name 
        FROM appointments a
        JOIN users p ON a.patient_id = p.id
        JOIN doctors d ON a.doctor_id = d.id
        JOIN users du ON d.user_id = du.id
        ORDER BY a.id DESC LIMIT ?
    ", "i", [$limit]);
        return $query ? $query->fetch_all(MYSQLI_ASSOC) : [];
    }


    public function getDoctorIdByUserId(int $userId): int
    {
        $query = $this->execute("SELECT id FROM doctors WHERE user_id = ?", "i", [$userId]);
        $doctor = $query ? $query->fetch_assoc() : null;
        return $doctor ? (int)$doctor["id"] : 0;
    }

    public function getDoctorMonthStats(int $doctorId): array
    {
        $query = $this->execute("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
        FROM appointments 
        WHERE doctor_id = ? AND MONTH(appt_date) = MONTH(CURDATE()) AND YEAR(appt_date) = YEAR(CURDATE())
    ", "i", [$doctorId]);
        return $query ? $query->fetch_assoc() : ["total" => 0, "pending" => 0, "completed" => 0];
    }

    public function getUpcomingAppointmentsByDoctor(int $doctorId, int $limit = 5): array
    {
        $query = $this->execute("
        SELECT a.*, p.name AS patient_name 
        FROM appointments a
        JOIN users p ON a.patient_id = p.id
        WHERE a.doctor_id = ? AND (a.appt_date > CURDATE() OR (a.appt_date = CURDATE() AND a.appt_time >= CURTIME()))
        AND a.status IN ('pending', 'confirmed')
        ORDER BY a.appt_date ASC, a.appt_time ASC LIMIT ?
    ", "ii", [$doctorId, $limit]);
        return $query ? $query->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getActiveAppointmentsByPatient(int $patientId): array
    {
        $query = $this->execute("
        SELECT a.*, du.name AS doctor_name 
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.id
        JOIN users du ON d.user_id = du.id
        WHERE a.patient_id = ? AND a.status IN ('pending', 'confirmed')
        ORDER BY a.appt_date ASC, a.appt_time ASC
    ", "i", [$patientId]);
        return $query ? $query->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getCompletedCountByPatient(int $patientId): int
    {
        $query = $this->execute("SELECT COUNT(*) as total FROM appointments WHERE patient_id = ? AND status = 'completed'", "i", [$patientId]);
        if ($query) {
            $row = $query->fetch_assoc();
            return (int)($row['total'] ?? 0);
        }
        return 0;
    }

    public function getPrescriptionsCountByPatient(int $patientId): int
    {
        $query = $this->execute("
        SELECT COUNT(*) as total FROM prescriptions pr
        JOIN appointments a ON pr.appointment_id = a.id
        WHERE a.patient_id = ?
    ", "i", [$patientId]);
        if ($query) {
            $row = $query->fetch_assoc();
            return (int)($row['total'] ?? 0);
        }
        return 0;
    }
}
