<?php

$pageTitle = "Prescription";

require_once __DIR__
    . "/../partials/header.php";

require_once __DIR__
    . "/../partials/navbar.php";

require_once __DIR__
    . "/../partials/sidebar.php";

?>

<div class="content-wrapper">

    <section class="content p-3">

        <div class="card">

            <div class="card-header">

                <h3>
                    Prescription Details
                </h3>

            </div>

            <div class="card-body">

                <p>
                    Diagnosis:
                </p>

                <div class="border p-2 mb-3">

                    <?= nl2br(
                        htmlspecialchars(
                            $prescription["diagnosis"]
                        )
                    ) ?>

                </div>

                <p>
                    Medications:
                </p>

                <div class="border p-2 mb-3">

                    <?= nl2br(
                        htmlspecialchars(
                            $prescription["medications"]
                        )
                    ) ?>

                </div>

                <p>
                    Notes:
                </p>

                <div class="border p-2">

                    <?= nl2br(
                        htmlspecialchars(
                            $prescription["notes"] ?? ""
                        )
                    ) ?>

                </div>

            </div>

        </div>

    </section>

</div>

<?php
require_once __DIR__
    . "/../partials/footer.php";