<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>
<?= $pageTitle ?? "ClinicDesk" ?>
</title>

<!-- AdminLTE CSS -->
<link rel="stylesheet"
href="<?= BASE_URL ?>/public/assets/adminlte/plugins/fontawesome-free/css/all.min.css">

<link rel="stylesheet"
href="<?= BASE_URL ?>/public/assets/adminlte/dist/css/adminlte.min.css">

</head>

<body class="hold-transition sidebar-mini">

<div class="wrapper">