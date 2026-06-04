<?php

require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../models/PrescriptionModel.php";

class PrescriptionController
{
    private PrescriptionModel $prescriptionModel;

    public function __construct()
    {
        $this->prescriptionModel =
            new PrescriptionModel();
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
            ->findByAppointment(
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

    
}