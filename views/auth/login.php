<?php

$pageTitle="Login";

require_once __DIR__
."/../partials/header.php";
?>

<div class="login-box mx-auto">

<div class="card">

<div class="card-body">

<h3 class="text-center mb-4">
ClinicDesk
</h3>

<form method="POST"
action="index.php?page=auth&action=login">

<input
type="hidden"
name="csrf_token"
value="<?= CSRF::generateToken() ?>"
>

<div class="input-group mb-3">

<input
type="email"
name="email"
class="form-control"
placeholder="Email"
required
>

</div>

<div class="input-group mb-3">

<input
type="password"
name="password"
class="form-control"
placeholder="Password"
required
>

</div>

<button
class="btn btn-primary btn-block"
type="submit"
>
Login
</button>

</form>

</div>

</div>

</div>

<?php
require_once __DIR__
."/../partials/footer.php";
?>