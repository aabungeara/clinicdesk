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
        (int)($_GET["id"] ?? 0);

    if (
        !$this->appointmentModel
            ->canAddPrescription(
                $appointmentId,
                Auth::id()
            )
    ) {

        require_once __DIR__
            . "/../views/errors/403.php";

        exit;
    }

    if (
        $this->prescriptionModel
            ->exists($appointmentId)
    ) {

        $_SESSION["flash"] =
            "Prescription already exists";

        redirect(
            "index.php?page=appointments"
        );
    }

    require_once __DIR__
        . "/../views/prescriptions/create.php";
}
}
