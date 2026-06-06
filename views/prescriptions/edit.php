<?php

$pageTitle = "Edit Prescription";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
$prescription = $prescription ?? [];
?>

<div class="content-wrapper">

    <section class="content p-3">

        <div class="card card-outline card-warning">

            <div class="card-header">

                <h3 class="card-title text-bold text-warning">

                    <i class="fas fa-file-prescription mr-2"></i>
                    Edit Prescription

                </h3>

            </div>

            <form
                method="POST"
                action="index.php?page=prescriptions&action=update"
                enctype="multipart/form-data">

                <input
                    type="hidden"
                    name="id"
                    value="<?= $prescription["id"] ?>">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= CSRF::generateToken() ?>">

                <div class="card-body">

                    <div class="form-group">

                        <label>
                            Diagnosis
                        </label>

                        <textarea
                            name="diagnosis"
                            class="form-control"
                            rows="4"
                            required><?= htmlspecialchars($prescription["diagnosis"]) ?></textarea>

                    </div>

                    <div class="form-group">

                        <label>
                            Medications
                        </label>

                        <textarea
                            name="medications"
                            class="form-control"
                            rows="4"
                            required><?= htmlspecialchars($prescription["medications"]) ?></textarea>

                    </div>

                    <div class="form-group">

                        <label>
                            Notes
                        </label>

                        <textarea
                            name="notes"
                            class="form-control"
                            rows="4"><?= htmlspecialchars($prescription["notes"]) ?></textarea>

                    </div>

                    <div class="form-group">

                        <label>
                            Replace PDF File
                        </label>

                        <input
                            type="file"
                            name="prescription_file"
                            class="form-control"
                            accept=".pdf">

                        <?php if (!empty($prescription["file_path"])): ?>

                            <small class="text-success d-block mt-2">

                                Current PDF Attached

                            </small>

                        <?php endif; ?>

                    </div>

                </div>

                <div class="card-footer">

                    <button
                        type="submit"
                        class="btn btn-warning">

                        <i class="fas fa-save mr-1"></i>
                        Update Prescription

                    </button>

                    <a
                        href="index.php?page=appointments&action=schedule"
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