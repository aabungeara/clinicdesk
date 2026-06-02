<?php
Auth::requireRole("admin");

$pageTitle = "Create User";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

?>

<div class="content-wrapper">

    <section class="content-header">

        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>Create User</h1>
                </div>

                <div class="col-sm-6">

                    <ol class="breadcrumb float-sm-right">

                        <li class="breadcrumb-item">
                            <a href="index.php?page=dashboard">
                                Dashboard
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="index.php?page=users">
                                Users
                            </a>
                        </li>

                        <li class="breadcrumb-item active">
                            Create
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
                        New User
                    </h3>

                </div>

                <form
                    method="POST"
                    action="index.php?page=users&action=store">

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

                        <div class="form-group">

                            <label>Email</label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                required>

                        </div>

                        <div class="form-group">

                            <label>Phone</label>

                            <input
                                type="text"
                                name="phone"
                                class="form-control">

                        </div>

                        <div class="form-group">

                            <label>Temporary Password</label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                required>

                        </div>

                        <div class="form-group">

                            <label>Role</label>

                            <select
                                name="role"
                                id="role"
                                class="form-control">

                                <option value="patient">
                                    Patient
                                </option>

                                <option value="doctor">
                                    Doctor
                                </option>

                            </select>

                        </div>

                        <div
                            id="doctorFields"
                            style="display:none;">

                            <hr>

                            <h5>
                                Doctor Information
                            </h5>

                            <div class="form-group">

                                <label>
                                    Specialization
                                </label>

                                <select
                                    name="specialization_id"
                                    class="form-control">

                                    <?php foreach ($specializations as $spec): ?>

                                        <option
                                            value="<?= $spec["id"] ?>">

                                            <?= htmlspecialchars(
                                                $spec["name"]
                                            ) ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                            <div class="form-group">

                                <label>
                                    Consultation Fee
                                </label>

                                <input
                                    type="number"
                                    step="0.01"
                                    name="consultation_fee"
                                    class="form-control">

                            </div>

                            <div class="form-group">

                                <label>
                                    Available Days
                                </label>

                                <br>

                                <?php

                                $days = [
                                    "Sun",
                                    "Mon",
                                    "Tue",
                                    "Wed",
                                    "Thu",
                                    "Fri",
                                    "Sat"
                                ];

                                foreach ($days as $day):

                                ?>

                                    <div
                                        class="form-check form-check-inline">

                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="available_days[]"
                                            value="<?= $day ?>">

                                        <label
                                            class="form-check-label">

                                            <?= $day ?>

                                        </label>

                                    </div>

                                <?php endforeach; ?>

                            </div>

                            <div class="form-group">

                                <label>Bio</label>

                                <textarea
                                    name="bio"
                                    class="form-control"
                                    rows="4"></textarea>

                            </div>

                        </div>

                    </div>

                    <div class="card-footer">

                        <button
                            type="submit"
                            class="btn btn-success">

                            Save User

                        </button>

                        <a
                            href="index.php?page=users"
                            class="btn btn-secondary">

                            Cancel

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </section>

</div>

<script>

document
    .getElementById("role")
    .addEventListener(
        "change",
        function () {

            document
                .getElementById(
                    "doctorFields"
                )
                .style.display =
                    this.value === "doctor"
                        ? "block"
                        : "none";
        }
    );

</script>

<?php

require_once __DIR__ . "/../partials/footer.php";

?>