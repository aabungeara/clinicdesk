<?php

require_once __DIR__ . "/BaseModel.php";

class PrescriptionModel extends BaseModel
{
    public function findByAppointmentId(
        int $apptId
    ): ?array {

        $result = $this->execute(
            "
            SELECT *
            FROM prescriptions
            WHERE appointment_id=?
            ",
            "i",
            [$apptId]
        );

        return $result->fetch_assoc()
            ?: null;
    }

   public function create(
    array $data
): bool {

    return $this->execute(
        "
        INSERT INTO prescriptions
        (
            appointment_id,
            diagnosis,
            medications,
            notes,
            file_path
        )
        VALUES
        (
            ?,?,?,?,?
        )
        ",
        "issss",
        [
            $data["appointment_id"],
            $data["diagnosis"],
            $data["medications"],
            $data["notes"],
            $data["file_path"]
        ]
    );
    return $result === true;
}

    public function update(
        int $id,
        array $data
    ): bool {

        $result = $this->execute(
            "
            UPDATE prescriptions
            SET
                diagnosis=?,
                medications=?,
                notes=?,
                file_path=?
            WHERE id=?
            ",
            "ssssi",
            [
                $data["diagnosis"],
                $data["medications"],
                $data["notes"] ?? null,
                $data["file_path"] ?? null,
                $id
            ]
        );

        return $result === true;
    }

    public function getByPatient(int $patientId): array
    {

        $result = $this->execute(
            "
            SELECT
                pr.*,
                a.appt_date,
                a.appt_time,
                a.doctor_id,
                u.name AS doctor_name

            FROM prescriptions pr

            JOIN appointments a
                ON pr.appointment_id = a.id

            JOIN doctors d
                ON a.doctor_id = d.id

            JOIN users u
                ON d.user_id = u.id

            WHERE a.patient_id=?

            ORDER BY pr.created_at DESC
            ",
            "i",
            [$patientId]
        );

        return $result->fetch_all(
            MYSQLI_ASSOC
        );
    }

    public function existsByAppointment(
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

        return $result->num_rows > 0;
    }

    public function exists(
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

        return $result->num_rows > 0;
    }
}
