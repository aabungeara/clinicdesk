<?php

Auth::requireRole("admin");

$pageTitle = "Doctors";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

?>

<div class="content-wrapper">

    <section class="content p-3">

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">
                    Doctors List
                </h3>

            </div>

            <div class="card-body">

                <table
                    class="table table-bordered table-striped">

                    <thead>

                        <tr>

                            <th>ID</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Specialization</th>
                            <th>Phone</th>
                            <th>Fee</th>
                            <th>Available Days</th>
                            <th>Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($doctors as $doctor): ?>

                            <tr>

                                <td>
                                    <?= $doctor["id"] ?>
                                </td>
                                <td>

                                    <?php if (!empty($doctor["photo"])): ?>

                                        <img
                                            src="public/uploads/doctor_photos/<?= htmlspecialchars($doctor["photo"]) ?>"
                                            width="60"
                                            height="60"
                                            style="object-fit:cover;border-radius:50%;">

                                    <?php else: ?>

                                        <img
                                            src="public/uploads/doctor_photos/default.png"
                                            width="60"
                                            height="60"
                                            style="object-fit:cover;border-radius:50%;">

                                    <?php endif; ?>

                                </td>
                                <td>
                                    <?= htmlspecialchars(
                                        $doctor["doctor_name"]
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $doctor["specialization_name"]
                                    ) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars(
                                        $doctor["phone"] ?? ""
                                    ) ?>
                                </td>

                                <td>
                                    <?= $doctor["consultation_fee"] ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $doctor["available_days"]
                                    ) ?>
                                </td>

                                <td>

                                    <a
                                        href="index.php?page=doctors&action=edit&id=<?= $doctor["id"] ?>"
                                        class="btn btn-warning btn-sm">

                                        Edit

                                    </a>

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