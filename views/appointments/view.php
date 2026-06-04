<?php

$pageTitle = "Appointment Detail";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

?>

<div class="content-wrapper">

    <section class="content p-3">

        <div class="card">

            <div class="card-header">

                <h3>
                    Appointment Detail
                </h3>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6">

                        <p>
                            <strong>Patient:</strong>
                            <?= htmlspecialchars($appointment["patient_name"]) ?>
                        </p>

                        <p>
                            <strong>Doctor:</strong>
                            <?= htmlspecialchars($appointment["doctor_name"]) ?>
                        </p>

                        <p>
                            <strong>Date:</strong>
                            <?= $appointment["appt_date"] ?>
                        </p>

                    </div>

                    <div class="col-md-6">

                        <p>
                            <strong>Time:</strong>
                            <?= $appointment["appt_time"] ?>
                        </p>

                        <p>
                            <strong>Status:</strong>
                            <?= ucfirst($appointment["status"]) ?>
                        </p>

                    </div>

                </div>

                <hr>

                <p>
                    <strong>Reason:</strong>
                </p>

                <p>
                    <?= nl2br(
                        htmlspecialchars(
                            $appointment["reason"]
                        )
                    ) ?>
                </p>

                <p>
                    <strong>Doctor Notes:</strong>
                </p>

                <p>
                    <?= nl2br(
                        htmlspecialchars(
                            $appointment["doctor_notes"] ?? ""
                        )
                    ) ?>
                </p>

            </div>

            <div class="card-footer">

                <a
                    href="index.php?page=appointments&action=myAppointments"
                    class="btn btn-secondary">

                    Back

                </a>

            </div>

        </div>

    </section>

</div>

<?php
require_once __DIR__ . "/../partials/footer.php";
?>