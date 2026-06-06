<?php

require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../models/AppointmentModel.php";
require_once __DIR__ . "/../models/DoctorModel.php";

class ReportController
{
    private AppointmentModel $appointmentModel;
    private DoctorModel $doctorModel;

    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel();
        $this->doctorModel = new DoctorModel();
    }

    public function index(): void
    {
        Auth::requireRole("admin");

        $doctors = $this->doctorModel->getAllDoctors();

        $startDate = $_GET["start_date"] ?? "";
        $endDate   = $_GET["end_date"] ?? "";
        $doctorId  = $_GET["doctor_id"] ?? "";
        $status    = $_GET["status"] ?? "";

        $reportData = [];
        $errors = [];

        // التحقق من المدخلات إذا تم إرسال الفلتر
        if (isset($_GET["filter"])) {
            if (empty($startDate) || empty($endDate)) {
                $errors[] = "Both Start Date and End Date are strictly required.";
            } elseif (strtotime($startDate) > strtotime($endDate)) {
                $errors[] = "Start Date cannot be greater than End Date.";
            }

            if (empty($errors)) {
                // بناء الاستعلام الديناميكي للتقرير
                $sql = "
                    SELECT 
                        p.name AS patient_name,
                        du.name AS doctor_name,
                        s.name AS specialization,
                        a.appt_date,
                        a.appt_time,
                        a.status,
                        a.reason
                    FROM appointments a
                    JOIN users p ON a.patient_id = p.id
                    JOIN doctors d ON a.doctor_id = d.id
                    JOIN users du ON d.user_id = du.id
                    JOIN specializations s ON d.specialization_id = s.id
                    WHERE a.appt_date BETWEEN ? AND ?
                ";

                $params = [$startDate, $endDate];
                $types = "ss";

                if (!empty($doctorId)) {
                    $sql .= " AND a.doctor_id = ?";
                    $params[] = (int)$doctorId;
                    $types .= "i";
                }

                if (!empty($status)) {
                    $sql .= " AND a.status = ?";
                    $params[] = $status;
                    $types .= "s";
                }

                $sql .= " ORDER BY a.appt_date ASC, a.appt_time ASC";

                $result = $this->appointmentModel->execute($sql, $types, $params);
                $reportData = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

                // إذا طلب المستخدم تصدير للملف الفوري CSV
                if (isset($_GET["export"]) && $_GET["export"] === "csv") {
                    $this->exportToCSV($reportData);
                }
            }
        }

        require_once __DIR__ . "/../views/reports/index.php";
    }

    private function exportToCSV(array $data): void
    {
        // تهيئة الـ Headers للمتصفح لتنزيل الملف فوراً دون تخزين
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Clinic_Report_' . date('Ymd_His') . '.csv');

        $output = fopen('php://output', 'w');

        // كتابة سطر العناوين الرئيسي (Header Row)
        fputcsv($output, ['Patient Name', 'Doctor Name', 'Specialization', 'Date', 'Time', 'Status', 'Reason']);

        // كتابة السطور والبيانات الحية
        foreach ($data as $row) {
            fputcsv($output, [
                $row['patient_name'],
                $row['doctor_name'],
                $row['specialization'],
                $row['appt_date'],
                $row['appt_time'],
                ucfirst($row['status']),
                $row['reason'] ?? ''
            ]);
        }

        fclose($output);
        exit();
    }
}