<?php

require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../models/UserModel.php";

class AuthController
{
    public function login()
    {
        session_start();

        if (Auth::check()) {
            redirect("index.php?page=dashboard");
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $this->handleLogin();
            return;
        }

        require_once __DIR__
            . "/../views/auth/login.php";
    }

    private function handleLogin(): void
    {

        if (
            !CSRF::validateToken(
                $_POST["csrf_token"] ?? ""
            )
        ) {
            $_SESSION["flash"] =
                "Invalid CSRF token";

            redirect(
                "index.php?page=auth&action=login"
            );
        }

        $email =
            trim($_POST["email"] ?? "");

        $password =
            $_POST["password"] ?? "";

        $model =
            new UserModel();

        $user =
            $model->findByEmail(
                $email
            );

        if (
            !$user ||
            !password_verify(
                $password,
                $user["password"]
            )
        ) {

            $_SESSION["flash"] =
                "Invalid email or password";

            redirect(
                "index.php?page=auth&action=login"
            );
        }

        if (
            (int)$user["is_active"] !== 1
        ) {

            $_SESSION["flash"] =
                "Account disabled";

            redirect(
                "index.php?page=auth&action=login"
            );
        }

        Auth::login($user);

        switch ($user["role"]) {

            case "admin":
                redirect(
                    "index.php?page=dashboard"
                );
                break;

            case "doctor":
                redirect(
                    "index.php?page=dashboard"
                );
                break;

            case "patient":
                redirect(
                    "index.php?page=dashboard"
                );
                break;

            default:
                Auth::logout();
        }
    }

    public function logout(): void
    {   
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {

            redirect(
                "index.php?page=dashboard"
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
                "index.php?page=dashboard"
            );
        }

        Auth::logout();
    }
}
