<?php

require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../models/PrescriptionModel.php";
require_once __DIR__. "/../models/AppointmentModel.php";

class PrescriptionController
{
    private PrescriptionModel $prescriptionModel;
    private AppointmentModel $appointmentModel;

    public function __construct()
    {
        $this->prescriptionModel =
            new PrescriptionModel();
        $this->appointmentModel =
            new AppointmentModel();
    }

    public function index(): void
    {
        Auth::requireRole(
            "doctor"
        );

        require_once __DIR__
            . "/../views/prescriptions/index.php";
    }

    public function view(): void
    {
        Auth::requireRole(
            "patient",
            "doctor",
            "admin"
        );

        $appointmentId =
            (int)($_GET["appointment_id"] ?? 0);

        $prescription =
            $this->prescriptionModel
            ->findByAppointmentId(
                $appointmentId
            );

        if (!$prescription) {

            $_SESSION["flash"] =
                "Prescription not found";

            redirect(
                "index.php?page=appointments"
            );
        }

        require_once __DIR__
            . "/../views/prescriptions/view.php";
    }

    public function myPrescriptions(): void
    {
        Auth::requireRole(
            "patient"
        );

        require_once __DIR__
            . "/../views/prescriptions/index.php";
    }

    public function create(): void
{
    Auth::requireRole("doctor");

    $appointmentId =
        (int)($_GET["appointment_id"] ?? 0);

    $appointmentModel =
        new AppointmentModel();

    $appointment =
        $appointmentModel
        ->findById(
            $appointmentId
        );

    if (
        !$appointment ||
        $appointment["status"] !== "completed"
    ) {

        $_SESSION["flash_error"] =
            "Appointment not completed";

        redirect(
            "index.php?page=appointments"
        );
    }

    require_once __DIR__
        . "/../views/prescriptions/create.php";
}

public function store(): void
{
    Auth::requireRole("doctor");

    if (
        !CSRF::validateToken(
            $_POST["csrf_token"] ?? ""
        )
    ) {

        die("Invalid CSRF");
    }

    $data = [

        "appointment_id" =>
            (int)$_POST["appointment_id"],

        "diagnosis" =>
            trim($_POST["diagnosis"]),

        "medications" =>
            trim($_POST["medications"]),

        "notes" =>
            trim($_POST["notes"]),

        "file_path" => null
    ];

    $this->prescriptionModel
        ->create($data);

    $_SESSION["flash_success"] =
        "Prescription created";

    redirect(
        "index.php?page=appointments"
    );
}
}
