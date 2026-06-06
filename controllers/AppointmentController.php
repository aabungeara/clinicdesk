<?php

require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../core/helpers.php";

require_once __DIR__ . "/../models/AppointmentModel.php";
require_once __DIR__ . "/../models/DoctorModel.php";
require_once __DIR__
    . "/../models/PrescriptionModel.php";

class AppointmentController
{
    private AppointmentModel $appointmentModel;
    private DoctorModel $doctorModel;
    private PrescriptionModel $prescriptionModel;

    public function __construct()
    {
        $this->appointmentModel =
            new AppointmentModel();

        $this->doctorModel =
            new DoctorModel();
        $this->prescriptionModel =
            new PrescriptionModel();
    }

    public function book(): void
    {
        Auth::requireRole("patient");

        $doctors =
            $this->doctorModel
            ->getAllDoctors();

        require_once __DIR__
            . "/../views/appointments/book.php";
    }

    public function create(): void
    {
        $this->book();
    }
    public function myAppointments(): void
    {
        Auth::requireRole("patient");

        $page =
            max(
                1,
                (int)($_GET["p"] ?? 1)
            );

        $filters = [

            "status" =>
            $_GET["status"] ?? "",

            "date_from" =>
            $_GET["date_from"] ?? "",

            "date_to" =>
            $_GET["date_to"] ?? ""

        ];

        $appointments =
            $this->appointmentModel
            ->getByPatient(
                Auth::id(),
                $page,
                $filters
            );

        require_once __DIR__
            . "/../views/appointments/index.php";
    }
    public function store(): void
    {
        Auth::requireRole("patient");

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            redirect(
                "index.php?page=appointments&action=book"
            );
        }

        if (
            !CSRF::validateToken(
                $_POST["csrf_token"] ?? ""
            )
        ) {
            $_SESSION["flash"] =
                "Invalid CSRF token";

            redirect(
                "index.php?page=appointments&action=book"
            );
        }

        $doctorId =
            (int)($_POST["doctor_id"] ?? 0);

        $date =
            $_POST["appt_date"] ?? "";

        $time =
            $_POST["appt_time"] ?? "";

        $reason =
            trim($_POST["reason"] ?? "");

        if (
            strtotime($date)
            < strtotime(date("Y-m-d"))
        ) {

            $_SESSION["flash"] =
                "Appointment date cannot be in the past";

            redirect(
                "index.php?page=appointments&action=book"
            );
        }

        if (
            $this->appointmentModel
            ->hasConflict(
                $doctorId,
                $date,
                $time
            )
        ) {

            $_SESSION["flash"] =
                "This slot is already booked";

            redirect(
                "index.php?page=appointments&action=book"
            );
        }

        $this->appointmentModel->book([
            "patient_id" =>
            Auth::currentUser()["id"],

            "doctor_id" =>
            $doctorId,

            "appt_date" =>
            $date,

            "appt_time" =>
            $time,

            "reason" =>
            $reason
        ]);

        $_SESSION["flash"] =
            "Appointment booked successfully";

        redirect(
            "index.php?page=appointments&action=myAppointments"
        );
    }

    public function view(): void
    {
        Auth::requireLogin();

        $id =
            (int)($_GET["id"] ?? 0);

        $appointment =
            $this->appointmentModel
            ->findById($id);

        if (!$appointment) {

            $_SESSION["flash"] =
                "Appointment not found";

            redirect(
                "index.php?page=appointments&action=myAppointments"
            );
        }

        require_once __DIR__
            . "/../views/appointments/view.php";
    }

    public function cancel(): void
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            die("Method Not Allowed");
        }

        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            $_SESSION["flash"] = "Invalid CSRF Token";
            redirect("index.php?page=dashboard");
            return;
        }

        $id = (int)($_POST["id"] ?? 0);
        $appointment = $this->appointmentModel->findById($id);

        if (!$appointment) {
            $_SESSION["flash"] = "Appointment not found";
            redirect("index.php?page=dashboard");
            return;
        }

        $role = Auth::role();
        $userId = Auth::id();

       
        if ($role === "patient") {
            if ((int)$appointment["patient_id"] !== $userId) {
                die("403 - Unauthorized Action");
            }
            if ($appointment["status"] !== "pending") {
                $_SESSION["flash"] = "You can only cancel pending appointments";
                redirect("index.php?page=appointments&action=myAppointments");
                return;
            }
            
            $this->appointmentModel->cancel($id);
            $_SESSION["flash"] = "Appointment cancelled successfully";
            redirect("index.php?page=appointments&action=myAppointments");
            return;
        }

        
        if ($role === "doctor") {
            $doctor = $this->doctorModel->findByUserId($userId);
            if (!$doctor || (int)$appointment["doctor_id"] !== (int)$doctor["id"]) {
                die("403 - Unauthorized Action");
            }

            $this->appointmentModel->cancel($id);
            $_SESSION["flash"] = "Appointment cancelled by doctor";
            redirect("index.php?page=appointments&action=schedule");
            return;
        }

        
        if ($role === "admin") {
            $this->appointmentModel->cancel($id);
            $_SESSION["flash"] = "Appointment cancelled by admin";
            redirect("index.php?page=appointments&action=all");
            return;
        }
    }

    public function saveNotes(): void
    {
        Auth::requireRole("doctor");

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            die("Method Not Allowed");
        }

        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            $_SESSION["flash"] = "Invalid CSRF Token";
            redirect("index.php?page=appointments&action=schedule");
            return;
        }

        $id = (int)($_POST["id"] ?? 0);
        $notes = trim($_POST["doctor_notes"] ?? "");

        $appointment = $this->appointmentModel->findById($id);
        $doctor = $this->doctorModel->findByUserId(Auth::id());

        if (!$appointment || !$doctor || (int)$appointment["doctor_id"] !== (int)$doctor["id"]) {
            die("403 - Unauthorized Action");
        }

        $this->appointmentModel->updateStatus($id, $appointment["status"], $notes);

        $_SESSION["flash"] = "Doctor notes saved successfully";
        redirect("index.php?page=appointments&action=view&id=" . $id);
    }
    public function schedule(): void
    {
        Auth::requireRole("doctor");

        $doctor =
            $this->doctorModel
            ->findByUserId(
                Auth::id()
            );

        if (!$doctor) {

            $_SESSION["flash"] =
                "Doctor record not found";

            redirect(
                "index.php?page=dashboard"
            );
        }

        $doctorId =
            $doctor["id"];

        $todayAppointments =
            $this->appointmentModel
            ->getTodayByDoctor(
                $doctorId
            );

        $appointments =
            $this->appointmentModel
            ->getByDoctor(
                $doctorId,
                1,
                []
            );

        require_once __DIR__
            . "/../views/appointments/doctor_schedule.php";
    }

    public function confirm(): void
    {
        Auth::requireRole("doctor");

        $id =
            (int)($_GET["id"] ?? 0);

        $this->appointmentModel
            ->confirm($id);

        redirect(
            "index.php?page=appointments&action=schedule"
        );
    }

    public function complete(): void
    {
        Auth::requireRole("doctor");

        $id =
            (int)($_GET["id"] ?? 0);

        $this->appointmentModel
            ->complete($id);

        redirect(
            "index.php?page=appointments&action=schedule"
        );
    }
}
