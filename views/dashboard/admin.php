<?php

Auth::requireRole("admin");

$pageTitle = "Admin Dashboard";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">

<section class="content p-3">

<h1>Admin Dashboard</h1>

<p>Welcome <?= htmlspecialchars(
    Auth::currentUser()["name"]
) ?></p>

</section>

</div>

<?php
require_once __DIR__ . "/../partials/footer.php";
?>