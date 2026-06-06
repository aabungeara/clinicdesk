<?php

require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../models/AppAppointmentModel.php";
require_once __DIR__ . "/../models/DoctorModel.php";

class ReportController
{
    private AppAppointmentModel $appointmentModel;
    private DoctorModel $doctorModel;

    public function __construct()
    {
        $this->appointmentModel = new AppAppointmentModel();
        $this->doctorModel = new DoctorModel();
    }

    public function index(): void
    {
        // حظر الدخول إلا للمسؤولين فقط
        Auth::requireRole("admin");

        // جلب قائمة الأطباء لعرضها في القائمة المنسدلة (Dropdown)
        $doctors = $this->doctorModel->getAllDoctors();

        // استقبال وتطهير مدخلات الفلترة
        $startDate = $_GET["start_date"] ?? "";
        $endDate   = $_GET["end_date"] ?? "";
        $doctorId  = $_GET["doctor_id"] ?? "";
        $status    = $_GET["status"] ?? "";

        $reportData = [];
        $errors = [];

        // متغيرات ملخص البيانات (Summary row variables) لتبادلها مع الـ View
        $summary = [
            'total_shown' => 0,
            'counts' => ['pending' => 0, 'confirmed' => 0, 'completed' => 0, 'cancelled' => 0]
        ];

        // معالجة البيانات فقط عند الضغط على زر التصفية وتوليد التقارير
        if (isset($_GET["filter"])) {
            
            // 1. التحقق من المدخلات (Validation Criteria)
            if (empty($startDate) || empty($endDate)) {
                $errors[] = "Both Start Date and End Date are strictly required.";
            } elseif (strtotime($startDate) > strtotime($endDate)) {
                $errors[] = "Validation Error: Start Date cannot be greater than End Date (start_date <= end_date).";
            }

            // 2. بناء الاستعلام في حال نجاح التحقق وعدم وجود أخطاء
            if (empty($errors)) {
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

                // إضافة فلتر الطبيب إن وُجد (Optional)
                if (!empty($doctorId)) {
                    $sql .= " AND a.doctor_id = ?";
                    $params[] = (int)$doctorId;
                    $types .= "i";
                }

                // إضافة فلتر حالة الموعد إن وُجد (Optional)
                if (!empty($status)) {
                    $sql .= " AND a.status = ?";
                    $params[] = $status;
                    $types .= "s";
                }

                // ترتيب النتائج تصاعدياً بناءً على التاريخ والوقت
                $sql .= " ORDER BY a.appt_date ASC, a.appt_time ASC";

                // استدعاء الدالة المخصصة من الـ Model لتفادي مشاكل الـ Protected Methods
                $reportData = $this->appointmentModel->getAppointmentsReport($sql, $types, $params);

                // 3. احتساب قيم الملخص الإحصائي (Summary Row calculation)
                if (!empty($reportData)) {
                    foreach ($reportData as $row) {
                        $summary['total_shown']++;
                        if (array_key_exists($row['status'], $summary['counts'])) {
                            $summary['counts'][$row['status']]++;
                        }
                    }
                }

                // 4. تصدير ملف التقرير الفوري بصيغة CSV في حال وجود طلب تصدير
                if (isset($_GET["export"]) && $_GET["export"] === "csv") {
                    $this->exportToCSV($reportData);
                }
            }
        }

        // تمرير المتغيرات الحية إلى الـ View لعرضها داخل القالب الموحد لـ AdminLTE
        require_once __DIR__ . "/../views/reports/index.php";
    }

    /**
     * توليد وتصدير البيانات الحية مباشرة إلى مخرج المتصفح على هيئة ملف تحميل CSV
     */
    private function exportToCSV(array $data): void
    {
        // تنظيف دارة التخزين المؤقت للمخرجات لضمان سلامة هيكل الملف وحمايته من التداخل
        if (ob_get_length()) ob_end_clean();

        // إعداد ترويسات الاستجابة البرمجية (HTTP Headers) للتحميل المباشر
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Clinic_Administrative_Report_' . date('Ymd_His') . '.csv');

        // فتح مخرج تدفق المخرجات المباشر
        $output = fopen('php://output', 'w');

        // إضافة ترويسة الـ Byte Order Mark (BOM) لضمان توافق الحروف والترميز العالمي مع Microsoft Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // كتابة سطر العناوين الرئيسي للجدول (Header Row)
        fputcsv($output, ['Patient Name', 'Doctor Name', 'Specialization', 'Date', 'Time', 'Status', 'Reason']);

        // كتابة البيانات صفاً تلو الآخر باستخدام المعايير القياسية لدالة fputcsv
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

        // إغلاق التدفق البرمجي وإنهاء العملية فوراً لمنع تحميل مخرجات الـ HTML اللاحقة
        fclose($output);
        exit();
    }
}