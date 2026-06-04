<?php

$pageTitle = "Add Prescription";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

?>

<div class="content-wrapper">

    <section class="content p-3">

        <div class="card">

            <div class="card-header">

                <h3>
                    Add Prescription
                </h3>

            </div>

            <form
                method="POST"
                action="index.php?page=prescriptions&action=store"
                enctype="multipart/form-data">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= CSRF::generateToken() ?>">

                <input
                    type="hidden"
                    name="appointment_id"
                    value="<?= $appointment["id"] ?? 0 ?>">

                <div class="card-body">

                    <div class="form-group">

                        <label>
                            Diagnosis
                        </label>

                        <textarea
                            name="diagnosis"
                            class="form-control"
                            rows="4"
                            required></textarea>

                    </div>

                    <div class="form-group">

                        <label>
                            Medications
                        </label>

                        <textarea
                            name="medications"
                            class="form-control"
                            rows="4"
                            required></textarea>

                    </div>

                    <div class="form-group">

                        <label>
                            Notes
                        </label>

                        <textarea
                            name="notes"
                            class="form-control"
                            rows="3"></textarea>

                    </div>

                    <div class="form-group">

                        <label>
                            PDF File
                        </label>

                        <input
                            type="file"
                            name="prescription_file"
                            class="form-control">

                    </div>

                </div>

                <div class="card-footer">

                    <button
                        type="submit"
                        class="btn btn-success">

                        Save Prescription

                    </button>

                </div>

            </form>

        </div>

    </section>

</div>

<?php
require_once __DIR__
    . "/../partials/footer.php";
?>