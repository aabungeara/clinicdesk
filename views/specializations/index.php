<?php

Auth::requireRole("admin");

$pageTitle = "Specializations";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

$specializations = $specializations ?? [];
?>

<div class="content-wrapper">

<section class="content p-3">

<div class="card">

<div class="card-header">

<h3 class="card-title">
Specializations
</h3>

<a
href="index.php?page=specializations&action=create"
class="btn btn-primary btn-sm float-right">

Add Specialization

</a>

</div>

<div class="card-body">

<?php if (!empty($_SESSION["flash"])): ?>

<div class="alert alert-info">

<?= htmlspecialchars($_SESSION["flash"]) ?>

</div>

<?php unset($_SESSION["flash"]); ?>

<?php endif; ?>

<table class="table table-bordered">

<thead>

<tr>

<th>ID</th>
<th>Name</th>
<th>Actions</th>

</tr>

</thead>

<tbody>

<?php foreach ($specializations as $spec): ?>

<tr>

<td><?= $spec["id"] ?></td>

<td>
<?= htmlspecialchars($spec["name"]) ?>
</td>

<td>

<form
method="POST"
action="index.php?page=specializations&action=delete"
style="display:inline;">

<input
type="hidden"
name="csrf_token"
value="<?= CSRF::generateToken() ?>">

<input
type="hidden"
name="id"
value="<?= $spec["id"] ?>">

<button
type="submit"
class="btn btn-danger btn-sm">

Delete

</button>

</form>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>

</section>

</div>

<?php
require_once __DIR__ . "/../partials/footer.php";
?>