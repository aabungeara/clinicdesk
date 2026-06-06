<?php

require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../models/AppAppointmentModel.php";

class DashboardController
{
    private AppAppointmentModel $appointmentModel;

    public function __construct()
    {
        $this->appointmentModel = new AppAppointmentModel();
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

        $todayAppointmentsCount = $this->appointmentModel->getTodayAppointmentsCount();

        $weekStats = $this->appointmentModel->getWeekStatsByStatus();

        $recentAppointments = $this->appointmentModel->getRecentAppointments(5);

        require_once __DIR__ . "/../views/dashboard/admin.php";
    }

    private function doctorDashboard(): void
    {
        $userId = Auth::id();

        $doctorId = $this->appointmentModel->getDoctorIdByUserId($userId);

        $todayAppointments = $this->appointmentModel->getTodayByDoctor($doctorId);

        $monthStats = $this->appointmentModel->getDoctorMonthStats($doctorId);

        $upcomingAppointments = $this->appointmentModel->getUpcomingAppointmentsByDoctor($doctorId, 5);

        require_once __DIR__ . "/../views/dashboard/doctor.php";
    }

    private function patientDashboard(): void
    {
        $patientId = Auth::id();

        $myAppointments = $this->appointmentModel->getActiveAppointmentsByPatient($patientId);
        
        $completedCount = $this->appointmentModel->getCompletedCountByPatient($patientId);
        $prescriptionsCount = $this->appointmentModel->getPrescriptionsCountByPatient($patientId);

        $patientStats = [
            'total'     => count($myAppointments) + $completedCount, 
            'pending'   => count(array_filter($myAppointments, fn($a) => strtolower($a['status']) === 'pending')),
            'completed' => $completedCount
        ];

        $nextAppointment = !empty($myAppointments) ? $myAppointments[0] : null;

        require_once __DIR__ . "/../views/dashboard/patient.php";
    }
}