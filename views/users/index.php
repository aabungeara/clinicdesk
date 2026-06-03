<?php

Auth::requireRole("admin");

$pageTitle = "Users";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

?>

<div class="content-wrapper">

    <section class="content-header">

        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>Users Management</h1>
                </div>

                <div class="col-sm-6">

                    <ol class="breadcrumb float-sm-right">

                        <li class="breadcrumb-item">
                            <a href="index.php?page=dashboard">
                                Dashboard
                            </a>
                        </li>

                        <li class="breadcrumb-item active">
                            Users
                        </li>

                    </ol>

                </div>

            </div>

        </div>

    </section>

    <section class="content">

        <div class="container-fluid">

            <div class="card">

                <div class="card-header">

                    <h3 class="card-title">
                        Users List
                    </h3>

                    <div class="card-tools">

                        <a
                            href="index.php?page=users&action=create"
                            class="btn btn-primary btn-sm">

                            Add User

                        </a>

                    </div>

                </div>

                <div class="card-body">

                    <?php if (!empty($_SESSION["flash"])): ?>

                        <div class="alert alert-success">

                            <?= htmlspecialchars($_SESSION["flash"]) ?>

                        </div>

                        <?php unset($_SESSION["flash"]); ?>

                    <?php endif; ?>

                    <table class="table table-bordered table-striped">

                        <thead>

                            <tr>

                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php if (empty($users)): ?>

                                <tr>

                                    <td
                                        colspan="6"
                                        class="text-center">

                                        No users found

                                    </td>

                                </tr>

                            <?php else: ?>

                                <?php foreach ($users as $user): ?>

                                    <tr>

                                        <td>

                                            <?= $user["id"] ?>

                                        </td>

                                        <td>

                                            <?= htmlspecialchars(
                                                $user["name"]
                                            ) ?>

                                        </td>

                                        <td>

                                            <?= htmlspecialchars(
                                                $user["email"]
                                            ) ?>

                                        </td>

                                        <td>

                                            <?= htmlspecialchars(
                                                ucfirst(
                                                    $user["role"]
                                                )
                                            ) ?>

                                        </td>

                                        <td>

                                            <?php if (
                                                (int)$user["is_active"] === 1
                                            ): ?>

                                                <span class="badge badge-success">

                                                    Active

                                                </span>

                                            <?php else: ?>

                                                <span class="badge badge-danger">

                                                    Inactive

                                                </span>

                                            <?php endif; ?>

                                        </td>
                                        <td>

                                            <a
                                                href="index.php?page=users&action=edit&id=<?= $user["id"] ?>"
                                                class="btn btn-warning btn-sm">

                                                Edit

                                            </a>

                                            <form
                                                method="POST"
                                                action="index.php?page=users&action=toggle"
                                                style="display:inline;">

                                                <input
                                                    type="hidden"
                                                    name="csrf_token"
                                                    value="<?= CSRF::generateToken() ?>">

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= $user["id"] ?>">

                                                <button
                                                    type="submit"
                                                    class="btn btn-secondary btn-sm">

                                                    <?= (int)$user["is_active"] === 1
                                                        ? "Deactivate"
                                                        : "Activate" ?>

                                                </button>

                                            </form>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </section>

</div>

<?php

require_once __DIR__ . "/../partials/footer.php";

?>