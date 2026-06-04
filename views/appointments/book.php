<?php

$pageTitle = "Book Appointment";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

$timeSlots = [
    "09:00",
    "09:30",
    "10:00",
    "10:30",
    "11:00",
    "11:30",
    "12:00",
    "12:30",
    "13:00",
    "13:30",
    "14:00",
    "14:30",
    "15:00",
    "15:30",
    "16:00"
];

?>

<div class="content-wrapper">

    <section class="content p-3">

        <div class="card">

            <div class="card-header">
                <h3>Book Appointment</h3>
            </div>

            <div class="card-body">

                <form
                    method="POST"
                    action="index.php?page=appointments&action=store">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= CSRF::generateToken() ?>">

                    <div class="form-group">

                        <label>Doctor</label>

                        <select
                            name="doctor_id"
                            class="form-control"
                            required>

                            <option value="">
                                Select Doctor
                            </option>

                            <?php foreach ($doctors as $doctor): ?>

                                <option
                                    value="<?= $doctor["id"] ?>">

                                    <?= htmlspecialchars(
                                        $doctor["doctor_name"]
                                    ) ?>

                                    -
                                    <?= htmlspecialchars(
                                        $doctor["specialization_name"]
                                    ) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Date
                                </label>

                                <input
                                    type="date"
                                    name="appt_date"
                                    class="form-control"
                                    min="<?= date('Y-m-d') ?>"
                                    required>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Time Slot
                                </label>

                                <select
                                    name="appt_time"
                                    class="form-control"
                                    required>

                                    <?php foreach ($timeSlots as $slot): ?>

                                        <option value="<?= $slot ?>">
                                            <?= $slot ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                        </div>

                    </div>


                    <div class="form-group">

                        <label>Reason</label>

                        <textarea
                            name="reason"
                            rows="3"
                            class="form-control"></textarea>

                    </div>

                    <div class="card-footer d-flex">

                        <button
                            type="submit"
                            class="btn btn-success">

                            Book

                        </button>

                        <a
                            href="index.php?page=dashboard"
                            class="btn btn-secondary ml-auto">

                            Cancel

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </section>

</div>

<?php
require_once __DIR__
    . "/../partials/footer.php";
?>