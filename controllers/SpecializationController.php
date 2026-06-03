<?php

require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../models/SpecializationModel.php";

class SpecializationController
{
    private SpecializationModel $specializationModel;

    public function __construct()
    {
        $this->specializationModel =
            new SpecializationModel();
    }

    public function index(): void
    {
        Auth::requireRole("admin");

        $specializationModel = new SpecializationModel();
        $specializations =
            $this->specializationModel
                ->getAll();

        require_once __DIR__
            . "/../views/specializations/index.php";
    }

    public function create(): void
    {
        Auth::requireRole("admin");

        require_once __DIR__
            . "/../views/specializations/create.php";
    }

    public function store(): void
    {
        Auth::requireRole("admin");

        if (
            !CSRF::validateToken(
                $_POST["csrf_token"] ?? ""
            )
        ) {
            die("Invalid CSRF Token");
        }

        $name =
            trim($_POST["name"] ?? "");

        if ($name === "") {

            $_SESSION["flash"] =
                "Name is required";

            redirect(
                "index.php?page=specializations"
            );
        }

        $this->specializationModel
            ->create($name);

        $_SESSION["flash"] =
            "Specialization created successfully";

        redirect(
            "index.php?page=specializations"
        );
    }

    public function delete(): void
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
            (int)($_POST["id"] ?? 0);

        if (
            !$this->specializationModel
                ->isSafeToDelete($id)
        ) {

            $_SESSION["flash"] =
                "Cannot delete specialization because doctors are using it";

            redirect(
                "index.php?page=specializations"
            );
        }

        $this->specializationModel
            ->delete($id);

        $_SESSION["flash"] =
            "Specialization deleted successfully";

        redirect(
            "index.php?page=specializations"
        );
    }
}