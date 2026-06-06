<?php

require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../models/PrescriptionModel.php";
require_once __DIR__ . "/../models/AppointmentModel.php";
require_once __DIR__ . "/../models/DoctorModel.php";

class PrescriptionController
{
    private PrescriptionModel $prescriptionModel;
    private AppointmentModel $appointmentModel;
    private DoctorModel $doctorModel;

    public function __construct()
    {
        $this->prescriptionModel = new PrescriptionModel();
        $this->appointmentModel = new AppointmentModel();
        $this->doctorModel =new DoctorModel();
    }

    public function index(): void
    {
        Auth::requireRole("doctor");
        require_once __DIR__ . "/../views/prescriptions/index.php";
    }

    public function view(): void
    {
        Auth::requireRole(
            "patient",
            "doctor",
            "admin"
        );

        $prescriptionId =
            (int)($_GET["id"] ?? 0);

        $prescription =
            $this->prescriptionModel
            ->findById($prescriptionId);

        if (!$prescription) {

            $_SESSION["flash_error"] =
                "Prescription not found";

            redirect(
                "index.php?page=appointments&action=schedule"
            );
        }

        $appointment =
            $this->appointmentModel
            ->findById(
                $prescription["appointment_id"]
            );

        if (!$appointment) {

            die("Appointment not found");
        }
        require_once __DIR__
            . "/../views/prescriptions/view.php";
    }

    public function myPrescriptions(): void
    {
        Auth::requireRole("patient");

        $prescriptions = $this->prescriptionModel->getByPatient(Auth::id());

        require_once __DIR__ . "/../views/prescriptions/index.php";
    }

    public function create(): void
    {
        Auth::requireRole("doctor");

        $appointmentId = (int)($_GET["appointment_id"] ?? 0);

        $appointment = $this->appointmentModel->findById($appointmentId);

        if (!$appointment || $appointment["status"] !== "completed") {
            $_SESSION["flash_error"] = "Appointment not completed";
            redirect("index.php?page=appointments&action=schedule");
        }

        $existing = $this->prescriptionModel->findByAppointmentId($appointmentId);

        if ($existing) {
            $_SESSION["flash_error"] = "Prescription already exists";
            redirect("index.php?page=appointments&action=schedule");
        }

        require_once __DIR__ . "/../views/prescriptions/create.php";
    }

    public function store(): void
    {
        Auth::requireRole("doctor");

        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            die("Invalid CSRF");
        }

        $filePath = null;

        if (isset($_FILES["prescription_file"]) && $_FILES["prescription_file"]["error"] === 0) {
            if ($_FILES["prescription_file"]["size"] > 3 * 1024 * 1024) {
                $_SESSION["flash_error"] = "PDF must be less than 3MB";
                redirect("index.php?page=prescriptions&action=create&appointment_id=" . $_POST["appointment_id"]);
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES["prescription_file"]["tmp_name"]);
            finfo_close($finfo);

            if ($mime !== "application/pdf") {
                $_SESSION["flash_error"] = "Only PDF files are allowed";
                redirect("index.php?page=prescriptions&action=create&appointment_id=" . $_POST["appointment_id"]);
            }

            $fileName = "prescription_" . $_POST["appointment_id"] . "_" . time() . ".pdf";
            $uploadDir = __DIR__ . "/../public/uploads/prescriptions/";

            move_uploaded_file($_FILES["prescription_file"]["tmp_name"], $uploadDir . $fileName);
            $filePath = "uploads/prescriptions/" . $fileName;
        }

        $data = [
            "appointment_id" => (int)$_POST["appointment_id"],
            "diagnosis" => trim($_POST["diagnosis"]),
            "medications" => trim($_POST["medications"]),
            "notes" => trim($_POST["notes"]),
            "file_path" => $filePath
        ];

        $this->prescriptionModel->create($data);

        $_SESSION["flash_success"] = "Prescription created";
        redirect("index.php?page=appointments&action=schedule");
    }

    public function edit(): void
    {
        Auth::requireRole("doctor");

        $id = (int)($_GET["id"] ?? 0);
        $prescription = $this->prescriptionModel->findById($id);

        if (!$prescription) {
            $_SESSION["flash_error"] = "Prescription not found";
            redirect("index.php?page=appointments&action=schedule");
        }
        

        require_once __DIR__ . "/../views/prescriptions/edit.php";
    }

    public function update(): void
    {
        Auth::requireRole("doctor");

        if (
            !CSRF::validateToken(
                $_POST["csrf_token"] ?? ""
            )
        ) {
            die("Invalid CSRF");
        }

        $id =
            (int)$_POST["id"];

        $prescription =
            $this->prescriptionModel
            ->findById($id);

        if (!$prescription) {

            redirect(
                "index.php?page=appointments&action=schedule"
            );
        }

        $filePath =
            $prescription["file_path"];

        if (
            isset($_FILES["prescription_file"])
            &&
            $_FILES["prescription_file"]["error"] === 0
        ) {

            $finfo =
                finfo_open(
                    FILEINFO_MIME_TYPE
                );

            $mime =
                finfo_file(
                    $finfo,
                    $_FILES["prescription_file"]["tmp_name"]
                );

            finfo_close($finfo);

            if (
                $mime !==
                "application/pdf"
            ) {

                die("Only PDF files allowed");
            }

            if (
                !empty($filePath)
                &&
                file_exists(
                    __DIR__
                        . "/../public/"
                        . $filePath
                )
            ) {

                unlink(
                    __DIR__
                        . "/../public/"
                        . $filePath
                );
            }

            $fileName =
                "prescription_"
                . time()
                . ".pdf";

            move_uploaded_file(
                $_FILES["prescription_file"]["tmp_name"],
                __DIR__
                    . "/../public/uploads/prescriptions/"
                    . $fileName
            );

            $filePath =
                "uploads/prescriptions/"
                . $fileName;
        }

        $this->prescriptionModel
            ->update(
                $id,
                [
                    "diagnosis" =>
                    trim($_POST["diagnosis"]),

                    "medications" =>
                    trim($_POST["medications"]),

                    "notes" =>
                    trim($_POST["notes"]),

                    "file_path" =>
                    $filePath
                ]
            );

        $_SESSION["flash_success"] =
            "Prescription updated";

        redirect(
            "index.php?page=prescriptions&action=view&id="
                . $id
        );
    }

    public function download(): void
    {
        Auth::requireRole(
            "patient",
            "doctor",
            "admin"
        );

        $prescriptionId =
            (int)($_GET["id"] ?? 0);

        $prescription =
            $this->prescriptionModel
            ->findById($prescriptionId);

        if (!$prescription) {

            $_SESSION["flash_error"] =
                "Prescription not found";

            redirect(
                "index.php?page=dashboard"
            );
        }

        $appointment =
            $this->appointmentModel
            ->findById(
                $prescription["appointment_id"]
            );

        if (!$appointment) {

            $_SESSION["flash_error"] =
                "Appointment not found";

            redirect(
                "index.php?page=dashboard"
            );
        }

        $role =
            Auth::role();

        if ($role === "patient") {

            if (
                (int)$appointment["patient_id"]
                !==
                (int)Auth::id()
            ) {

                die("403 - Access Denied");
            }
        }

        if ($role === "doctor") {

            $doctorModel =
                new DoctorModel();

            $doctor =
                $doctorModel
                ->findByUserId(
                    Auth::id()
                );

            if (
                !$doctor ||
                (int)$appointment["doctor_id"]
                !==
                (int)$doctor["id"]
            ) {

                die("403 - Access Denied");
            }
        }

        if (
            empty($prescription["file_path"])
        ) {

            $_SESSION["flash_error"] =
                "PDF file not found";

            redirect(
                "javascript:history.back()"
            );
        }

        $fullPath =
            __DIR__
            . "/../public/"
            . $prescription["file_path"];

        if (!file_exists($fullPath)) {

            $_SESSION["flash_error"] =
                "File missing from server";

            redirect(
                "javascript:history.back()"
            );
        }

        header(
            "Content-Type: application/pdf"
        );

        header(
            'Content-Disposition: attachment; filename="prescription.pdf"'
        );

        header(
            "Content-Length: "
                . filesize($fullPath)
        );

        readfile($fullPath);

        exit;
    }
}
