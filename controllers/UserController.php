<?php

require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../models/UserModel.php";
require_once __DIR__ . "/../models/DoctorModel.php";
require_once __DIR__ . "/../models/SpecializationModel.php";

class UserController
{
    private UserModel $userModel;
    private DoctorModel $doctorModel;
    private SpecializationModel $specializationModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->doctorModel = new DoctorModel();
        $this->specializationModel = new SpecializationModel();
    }

    public function index(): void
    {
        
        Auth::requireRole("admin");
        
        $page = max(
            1,
            (int)($_GET["p"] ?? 1)
        );

        $role = $_GET["role"] ?? "";

        $users = $this->userModel
            ->getAllPaginated(
                $page,
                $role
            );
            

        require_once __DIR__
            . "/../views/users/index.php";
    }

    public function create(): void
    {
        Auth::requireRole("admin");

        $specializations =
            $this->specializationModel
                ->getAll();

        require_once __DIR__
            . "/../views/users/create.php";
    }

    public function store(): void
    {
        Auth::requireRole("admin");

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            redirect("index.php?page=users");
        }

        if (
            !CSRF::validateToken(
                $_POST["csrf_token"] ?? ""
            )
        ) {
            $_SESSION["flash"] =
                "Invalid CSRF token";

            redirect(
                "index.php?page=users&action=create"
            );
        }

        $name =
            trim($_POST["name"] ?? "");

        $email =
            filter_var(
                $_POST["email"] ?? "",
                FILTER_SANITIZE_EMAIL
            );

        $phone =
            trim($_POST["phone"] ?? "");

        $password =
            $_POST["password"] ?? "";

        $role =
            $_POST["role"] ?? "patient";

        $userId =
            $this->userModel->create([
                "name" => $name,
                "email" => $email,
                "password" => password_hash(
                    $password,
                    PASSWORD_DEFAULT
                ),
                "role" => $role,
                "phone" => $phone
            ]);

        if ($role === "doctor") {

            $this->doctorModel->create([
                "user_id" => $userId,
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
                    trim($_POST["bio"] ?? "")
            ]);
        }

        $_SESSION["flash"] =
            "User created successfully";

        redirect(
            "index.php?page=users"
        );
    }
}