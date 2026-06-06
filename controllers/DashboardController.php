<?php

require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../models/AppointmentModel.php";

class DashboardController
{
    private AppointmentModel $appointmentModel;

    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel();
    }

    public function index(): void
    {
        Auth::requireLogin();
        $role = Auth::role();

        switch ($role) {
            case "admin":
                $this->adminDashboard();
                break;
            case "doctor":
                $this->doctorDashboard();
                break;
            case "patient":
                $this->patientDashboard();
                break;
            default:
                redirect("index.php?page=auth&action=login");
        }
    }

    private function adminDashboard(): void
    {
        
        $rolesCount = $this->appointmentModel->getUsersCountByRole();

        // 2. مواعيد اليوم
        $todayAppointmentsCount = $this->appointmentModel->getTodayAppointmentsCount();

        // 3. مواعيد الأسبوع حسب الحالة
        $weekStats = $this->appointmentModel->getWeekStatsByStatus();

        // 4. آخر 5 مواعيد مسجلة
        $recentAppointments = $this->appointmentModel->getRecentAppointments(5);

        require_once __DIR__ . "/../views/dashboard/admin.php";
    }

    private function doctorDashboard(): void
    {
        $userId = Auth::id();

        // جلب معرف الطبيب (تم التعديل لاستدعاء الدالة الآمنة من الموديل)
        $doctorId = $this->appointmentModel->getDoctorIdByUserId($userId);

        // مواعيد اليوم للطبيب
        $todayAppointments = $this->appointmentModel->getTodayByDoctor($doctorId);

        // إحصائيات الشهر الحالي (الإجمالي، معلق، مكتمل)
        $monthStats = $this->appointmentModel->getDoctorMonthStats($doctorId);

        // المواعيد الـ 5 القادمة مرتبة حسب التاريخ والوقت
        $upcomingAppointments = $this->appointmentModel->getUpcomingAppointmentsByDoctor($doctorId, 5);

        require_once __DIR__ . "/../views/dashboard/doctor.php";
    }

    private function patientDashboard(): void
    {
        $patientId = Auth::id();

        // 1. جلب المواعيد الخاصة بالمريض (تخزينها بالاسم الصحيح للـ View)
        $myAppointments = $this->appointmentModel->getActiveAppointmentsByPatient($patientId);
        
        // 2. جلب الحسابات الإحصائية الإضافية من الموديل
        $completedCount = $this->appointmentModel->getCompletedCountByPatient($patientId);
        $prescriptionsCount = $this->appointmentModel->getPrescriptionsCountByPatient($patientId);

        // 3. تجميع الإحصائيات في المصفوفة التي يتوقعها الـ View
        $patientStats = [
            'total'     => count($myAppointments) + $completedCount, // إجمالي المواعيد (نشطة + مكتملة)
            'pending'   => count(array_filter($myAppointments, fn($a) => strtolower($a['status']) === 'pending')),
            'completed' => $completedCount
        ];

        // الموعد القادم الأقرب للمريض (إذا كنت تحتاجه في مكان آخر)
        $nextAppointment = !empty($myAppointments) ? $myAppointments[0] : null;

        // استدعاء الواجهة
        require_once __DIR__ . "/../views/dashboard/patient.php";
    }
}