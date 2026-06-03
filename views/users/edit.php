<?php

$pageTitle = "Edit User";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

$user = $user ?? [];

?>

<div class="content-wrapper">

    <section class="content p-3">

        <div class="card">

            <div class="card-header">

                <h3>Edit User</h3>

            </div>

            <form
                method="POST"
                action="index.php?page=users&action=update">

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

                <div class="card-footer">

                    <button
                        type="submit"
                        class="btn btn-success">

                        Save

                    </button>

                    <a
                        href="<?= h(url('users')) ?>"
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