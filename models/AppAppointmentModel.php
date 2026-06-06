<?php
require_once __DIR__ . "/AppointmentModel.php"; 

class AppAppointmentModel extends AppointmentModel
{

    public function hasConflict(int $doctorId, string $date, string $time): bool 
    {
        $result = $this->execute(
            "
            SELECT id
            FROM appointments
            WHERE doctor_id = ?
            AND appt_date = ?
            AND appt_time = ?
            AND status != 'cancelled'
            ",
            "iss",
            [$doctorId, $date, $time]
        );

        return $result && $result->num_rows > 0;
    }

    
    public function getAdminAppointmentStats(): array
    {
        $sql = "SELECT status, COUNT(*) as count FROM appointments GROUP BY status";
        $result = $this->execute($sql);
        $stats = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $stats[$row['status']] = (int)$row['count'];
            }
        }
        // Ensure all statuses are present, even if count is 0
        $allStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        foreach ($allStatuses as $status) {
            if (!isset($stats[$status])) {
                $stats[$status] = 0;
            }
        }
        return $stats;
    }


}