<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/core/helpers.php";
require_once __DIR__ . "/core/Auth.php";
require_once __DIR__ . "/core/CSRF.php";

$page = $_GET["page"] ?? "auth";
$action = $_GET["action"] ?? "login";

if ($page === "auth") {

    require_once __DIR__ . "/controllers/AuthController.php";

    $controller = new AuthController();

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        echo "Action Not Found";
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

    require_once __DIR__
        . "/controllers/UserController.php";

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
echo "Page Not Found";
