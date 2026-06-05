<?php

$pageTitle = "Prescription Details";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

?>

<div class="content-wrapper">

    <section class="content-header">

        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">

                    <h1>
                        Prescription Details
                    </h1>

                </div>

            </div>

        </div>

    </section>

    <section class="content">

        <div class="container-fluid">

            <div class="card card-primary">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-file-medical"></i>
                        Prescription Information

                    </h3>

                </div>

                <div class="card-body">

                    <div class="mb-4">

                        <h5 class="font-weight-bold">
                            Diagnosis
                        </h5>

                        <div class="border rounded p-3 bg-light">

                            <?= nl2br(
                                htmlspecialchars(
                                    $prescription["diagnosis"]
                                )
                            ) ?>

                        </div>

                    </div>

                    <div class="mb-4">

                        <h5 class="font-weight-bold">
                            Medications
                        </h5>

                        <div class="border rounded p-3 bg-light">

                            <?= nl2br(
                                htmlspecialchars(
                                    $prescription["medications"]
                                )
                            ) ?>

                        </div>

                    </div>

                    <div class="mb-4">

                        <h5 class="font-weight-bold">
                            Doctor Notes
                        </h5>

                        <div class="border rounded p-3 bg-light">

                            <?= nl2br(
                                htmlspecialchars(
                                    $prescription["notes"] ?? "No notes available"
                                )
                            ) ?>

                        </div>

                    </div>

                    <?php if (
                        !empty(
                            $prescription["file_path"]
                        )
                    ): ?>

                        <div class="mb-4">

                            <h5 class="font-weight-bold">
                                Attached PDF
                            </h5>

                            <a
                                href="index.php?page=prescriptions&action=download&id=<?= $prescription["appointment_id"] ?>"
                                class="btn btn-danger">

                                <i class="fas fa-file-pdf"></i>
                                Download Prescription PDF

                            </a>

                        </div>

                    <?php endif; ?>

                    <a
                        href="javascript:history.back()"
                        class="btn btn-secondary">

                        <i class="fas fa-arrow-left"></i>
                        Back

                    </a>

                </div>

            </div>

        </div>

    </section>

</div>

<?php
require_once __DIR__
    . "/../partials/footer.php";
?>