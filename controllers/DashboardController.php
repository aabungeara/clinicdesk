<?php

require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/helpers.php";

class DashboardController
{
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
        session_start();
        }

        if (!Auth::check()) {
            redirect("index.php?page=auth&action=login");
        }

        switch (Auth::role()) {

            case "admin":
                require_once __DIR__ . "/../views/dashboard/admin.php";
                break;

            case "doctor":
                require_once __DIR__ . "/../views/dashboard/doctor.php";
                break;

            case "patient":
                require_once __DIR__ . "/../views/dashboard/patient.php";
                break;

            default:
                require_once __DIR__ . "/../views/errors/403.php";
        }
    }
}