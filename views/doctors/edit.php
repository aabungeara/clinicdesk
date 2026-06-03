<?php

$pageTitle = "Edit Doctor";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

$selectedDays =
    explode(
        ",",
        $doctor["available_days"]
    );
?>

<div class="content-wrapper">

<section class="content p-3">

<div class="card">

<div class="card-header">
<h3>Edit Doctor</h3>
</div>

<form
method="POST"
enctype="multipart/form-data"
action="index.php?page=doctors&action=update">

<input
type="hidden"
name="csrf_token"
value="<?= CSRF::generateToken() ?>">

<input
type="hidden"
name="id"
value="<?= $doctor["id"] ?>">