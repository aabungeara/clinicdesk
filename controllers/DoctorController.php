<?php

require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../models/DoctorModel.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../models/SpecializationModel.php";

class DoctorController
{
    private DoctorModel $doctorModel;
    private SpecializationModel $specializationModel;


    public function __construct()
    {
        $this->doctorModel = new DoctorModel();
        $this->specializationModel = new SpecializationModel();
    }

    public function index(): void
    {
        Auth::requireRole("admin");

        $doctors =
            $this->doctorModel
            ->getAllDoctors();

        require_once __DIR__
            . "/../views/doctors/index.php";
    }

    public function edit(): void
    {
        Auth::requireRole("admin");

        $id =
            (int)($_GET["id"] ?? 0);

        $doctor =
            $this->doctorModel
            ->findById($id);

        if (!$doctor) {

            $_SESSION["flash"] =
                "Doctor not found";

            redirect(
                "index.php?page=doctors"
            );
        }

        $specializations =
            $this->specializationModel
            ->getAll();

        require_once __DIR__
            . "/../views/doctors/edit.php";
    }

    public function update(): void
    {
        Auth::requireRole("admin");

        if (
            !CSRF::validateToken(
                $_POST["csrf_token"] ?? ""
            )
        ) {
            die("Invalid CSRF Token");
        }

        $id =
            (int)$_POST["id"];

        $doctor =
            $this->doctorModel
            ->findById($id);

        if (!$doctor) {
            redirect(
                "index.php?page=doctors"
            );
        }

        $photo =
            $doctor["photo"] ?? null;

        if (
            !empty($_FILES["photo"]["name"])
        ) {

            $allowed =
                [
                    "image/jpeg",
                    "image/png"
                ];

            if (
                in_array(
                    $_FILES["photo"]["type"],
                    $allowed,
                    true
                )
                &&
                $_FILES["photo"]["size"]
                <= 1024 * 1024
            ) {

                $uploadDir =
                    __DIR__
                    . "/../public/uploads/doctor_photos/";

                if (
                    !is_dir(
                        $uploadDir
                    )
                ) {
                    mkdir(
                        $uploadDir,
                        0777,
                        true
                    );
                }

                $newName =
                    uniqid()
                    . "_"
                    . basename(
                        $_FILES["photo"]["name"]
                    );

                move_uploaded_file(
                    $_FILES["photo"]["tmp_name"],
                    $uploadDir . $newName
                );

                if (
                    !empty($photo)
                    &&
                    file_exists(
                        $uploadDir . $photo
                    )
                ) {
                    unlink(
                        $uploadDir . $photo
                    );
                }

                $photo = $newName;
            }
        }

        $this->doctorModel->update(
            $id,
            [
                "specialization_id" =>
                (int)$_POST["specialization_id"],

                "consultation_fee" =>
                $_POST["consultation_fee"],

                "available_days" =>
                implode(
                    ",",
                    $_POST["available_days"] ?? []
                ),

                "bio" =>
                trim(
                    $_POST["bio"] ?? ""
                ),

                "photo" =>
                $photo
            ]
        );

        $_SESSION["flash"] =
            "Doctor updated successfully";

        redirect(
            "index.php?page=doctors"
        );
    }
}
