<?php

Auth::requireRole("admin");

$pageTitle = "Create Specialization";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

?>

<div class="content-wrapper">

<section class="content p-3">

<div class="card">

<div class="card-header">

<h3>Create Specialization</h3>

</div>

<form
method="POST"
action="index.php?page=specializations&action=store">

<div class="card-body">

<input
type="hidden"
name="csrf_token"
value="<?= CSRF::generateToken() ?>">

<div class="form-group">

<label>Name</label>

<input
type="text"
name="name"
class="form-control"
required>

</div>

</div>

<div class="card-footer">

<button
type="submit"
class="btn btn-success">

Save

</button>

<a
href="index.php?page=specializations"
class="btn btn-secondary">

Cancel

</a>

</div>

</form>

</div>

</section>

</div>

<?php
require_once __DIR__ . "/../partials/footer.php";
?>