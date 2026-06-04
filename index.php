<?php

session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/core/helpers.php";
require_once __DIR__ . "/core/Auth.php";
require_once __DIR__ . "/core/CSRF.php";
require_once __DIR__ . "/config/constants.php";

$page = $_GET["page"] ?? "auth";
$action = $_GET["action"] ?? "login";

if ($page === "auth") {


    require_once __DIR__ . "/controllers/AuthController.php";

    $controller = new AuthController();

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        require_once __DIR__ . "/views/errors/404.php";
    }

    exit;
}



if ($page === "dashboard") {

    require_once __DIR__ . "/controllers/DashboardController.php";

    $controller =
        new DashboardController();

    $controller->index();

    exit;
}

if ($page === "users") {

    require_once __DIR__. "/controllers/UserController.php";

    $controller =
        new UserController();

    $action =
        $_GET["action"] ?? "index";

    if (
        method_exists(
            $controller,
            $action
        )
    ) {

        $controller->$action();

    } else {

        require_once __DIR__
            . "/views/errors/404.php";
    }

    exit;
}

if ($page === "doctors") {

    require_once __DIR__
        . "/controllers/DoctorController.php";

    $controller =
        new DoctorController();

    $action =
        $_GET["action"] ?? "index";

    if (
        method_exists(
            $controller,
            $action
        )
    ) {

        $controller->$action();

    } else {

        require_once __DIR__
            . "/views/errors/404.php";
    }

    exit;
}

if ($page === "specializations") {

    require_once __DIR__
        . "/controllers/SpecializationController.php";

    $controller =
        new SpecializationController();

    $action =
        $_GET["action"] ?? "index";

    if (
        method_exists(
            $controller,
            $action
        )
    ) {

        $controller->$action();

    } else {

        require_once __DIR__
            . "/views/errors/404.php";
    }

    exit;
}
if ($page === "appointments") {

    require_once __DIR__
        . "/controllers/AppointmentController.php";

    $controller =
        new AppointmentController();

    $action =
        $_GET["action"] ?? "book";

    if (
        method_exists(
            $controller,
            $action
        )
    ) {

        $controller->$action();

    } else {

        require_once __DIR__
            . "/views/errors/404.php";
    }

    exit;
}

if ($page === "prescriptions") {

    require_once __DIR__
        . "/controllers/PrescriptionController.php";

    $controller =
        new PrescriptionController();

    $action =
        $_GET["action"] ?? "index";

    if (
        method_exists(
            $controller,
            $action
        )
    ) {

        $controller->$action();

    } else {

        require_once __DIR__
            . "/views/errors/404.php";
    }

    exit;
}
require_once __DIR__ . "/views/errors/404.php";
