<?php

$pageTitle = "My Prescriptions";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

?>

<div class="content-wrapper">

    <section class="content-header">

        <div class="container-fluid">

            <h1>

                <i class="fas fa-file-prescription text-success mr-2"></i>
                My Prescriptions

            </h1>

        </div>

    </section>

    <section class="content">

        <div class="container-fluid">

            <div class="card card-outline card-success">

                <div class="card-header">

                    <h3 class="card-title">

                        Prescription Records

                    </h3>

                </div>

                <div class="card-body">

                    <table class="table table-bordered table-striped">

                        <thead>

                            <tr>

                                <th>Doctor</th>

                                <th>Date</th>

                                <th>Diagnosis</th>

                                <th>PDF</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php if (empty($prescriptions)): ?>

                                <tr>

                                    <td colspan="4" class="text-center">

                                        No prescriptions found

                                    </td>

                                </tr>

                            <?php else: ?>

                                <?php foreach ($prescriptions as $prescription): ?>

                                    <tr>

                                        <td>

                                            Dr.
                                            <?= htmlspecialchars($prescription["doctor_name"]) ?>

                                        </td>

                                        <td>

                                            <?= htmlspecialchars(
                                                $prescription["appt_date"]
                                            ) ?>

                                        </td>

                                        <td>

                                            <?= htmlspecialchars(
                                                mb_strimwidth(
                                                    $prescription["diagnosis"],
                                                    0,
                                                    50,
                                                    "..."
                                                )
                                            ) ?>

                                        </td>

                                        <td>

                                            <?php if (!empty($prescription["file_path"])): ?>

                                                <a
                                                    href="index.php?page=prescriptions&action=download&id=<?= $prescription["id"] ?>"
                                                    class="btn btn-danger btn-sm">

                                                    <i class="fas fa-file-pdf"></i>
                                                    Download

                                                </a>

                                            <?php else: ?>

                                                <span class="text-muted">

                                                    No File

                                                </span>

                                            <?php endif; ?>

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