<?php

$pageTitle = "Edit Prescription";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

?>

<div class="content-wrapper">

    <section class="content p-3">

        <div class="card">

            <div class="card-header">

                <h3>Edit Prescription</h3>

            </div>

            <form
                method="POST"
                action="index.php?page=prescriptions&action=update">

                <input
                    type="hidden"
                    name="id"
                    value="<?= $prescription["id"] ?>">

                <div class="card-body">

                    <div class="form-group">

                        <label>Diagnosis</label>

                        <textarea
                            name="diagnosis"
                            class="form-control"
                            rows="4"><?= htmlspecialchars($prescription["diagnosis"]) ?></textarea>

                    </div>

                    <div class="form-group">

                        <label>Medications</label>

                        <textarea
                            name="medications"
                            class="form-control"
                            rows="4"><?= htmlspecialchars($prescription["medications"]) ?></textarea>

                    </div>

                    <div class="form-group">

                        <label>Notes</label>

                        <textarea
                            name="notes"
                            class="form-control"
                            rows="4"><?= htmlspecialchars($prescription["notes"]) ?></textarea>

                    </div>

                </div>

                <div class="card-footer">

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Update Prescription

                    </button>

                </div>

            </form>

        </div>

    </section>

</div>

<?php
require_once __DIR__ . "/../partials/footer.php";
?>