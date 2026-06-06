<?php

$pageTitle = "Edit Profile";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

$user = $user ?? [];

?>

<div class="content-wrapper">

    <section class="content p-3">

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['flash']); ?>
                <?php unset($_SESSION['flash']); ?>
            </div>
        <?php endif; ?>

        <div class="card">

            <div class="card-header">

                <h3>Edit Profile</h3>

            </div>

            <form
                method="POST"
                action="index.php?page=profile&action=updateProfile">

                <div class="card-body">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= CSRF::generateToken() ?>">

                    <input
                        type="hidden"
                        name="id"
                        value="<?= $user["id"] ?>">

                    <div class="form-group">

                        <label>Name</label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="<?= htmlspecialchars($user["name"]) ?>"
                            required>

                    </div>

                    <div class="form-group">

                        <label>Phone</label>

                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            value="<?= htmlspecialchars($user["phone"] ?? "") ?>">

                    </div>

                </div>
                <div class="card-footer d-flex">

                    <button
                        type="submit"
                        class="btn btn-success">

                        Save Changes

                    </button>

                    <a
                        href="index.php?page=dashboard"
                        class="btn btn-secondary ml-auto ms-auto">

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