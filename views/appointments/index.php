<?php

$pageTitle = "My Appointments";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
require_once __DIR__ . "/../../models/AppAppointmentModel.php";

$appointmentModel = new AppAppointmentModel();
$appointments = $appointmentModel->getAllAppointmentsForAdmin(
    1,
    [
        "patient_name" => $_GET["patient_name"] ?? "",
        "doctor_id"    => $_GET["doctor_id"] ?? "",
        "status"       => $_GET["status"] ?? "",
        "date_from"    => $_GET["date_from"] ?? "",
        "date_to"      => $_GET["date_to"] ?? ""
    ]
);
?>

<div class="content-wrapper">

    <section class="content p-3">

        <div class="card">

            <div class="card-header d-flex">

                <h3 class="mr-auto">
                    My Appointments
                </h3>

                <a
                    href="index.php?page=appointments&action=book"
                    class="btn btn-primary">

                    Book Appointment

                </a>

            </div>

            <div class="card-body">

                <form
                    method="GET"
                    class="row mb-4">

                    <input
                        type="hidden"
                        name="page"
                        value="appointments">

                    <input
                        type="hidden"
                        name="action"
                        value="myAppointments">

                    <div class="col-md-3">

                        <select
                            name="status"
                            class="form-control">

                            <option value="">
                                All Statuses
                            </option>

                            <option
                                value="pending"
                                <?= ($_GET["status"] ?? "") === "pending"
                                    ? "selected"
                                    : "" ?>>
                                Pending
                            </option>

                            <option
                                value="confirmed"
                                <?= ($_GET["status"] ?? "") === "confirmed"
                                    ? "selected"
                                    : "" ?>>
                                Confirmed
                            </option>

                            <option
                                value="completed"
                                <?= ($_GET["status"] ?? "") === "completed"
                                    ? "selected"
                                    : "" ?>>
                                Completed
                            </option>

                            <option
                                value="cancelled"
                                <?= ($_GET["status"] ?? "") === "cancelled"
                                    ? "selected"
                                    : "" ?>>
                                Cancelled
                            </option>

                        </select>

                    </div>

                    <div class="col-md-3">

                        <input
                            type="date"
                            name="date_from"
                            class="form-control"
                            value="<?= htmlspecialchars($_GET["date_from"] ?? "") ?>">

                    </div>

                    <div class="col-md-3">

                        <input
                            type="date"
                            name="date_to"
                            class="form-control"
                            value="<?= htmlspecialchars($_GET["date_to"] ?? "") ?>">

                    </div>

                    <div class="col-md-3">

                        <button
                            class="btn btn-info">

                            Search

                        </button>

                    </div>

                </form>

                <table
                    class="table table-bordered table-striped">

                    <thead>

                        <tr>

                            <th>Doctor</th>
                            <th>Specialization</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($appointments as $appointment): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars(
                                        $appointment["doctor_name"]
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $appointment["specialization_name"]
                                    ) ?>
                                </td>

                                <td>
                                    <?= $appointment["appt_date"] ?>
                                </td>

                                <td>
                                    <?= substr(
                                        $appointment["appt_time"],
                                        0,
                                        5
                                    ) ?>
                                </td>

                                <td>

                                    <?php

                                    $badge = "secondary";

                                    switch ($appointment["status"]) {

                                        case "pending":
                                            $badge = "warning";
                                            break;

                                        case "confirmed":
                                            $badge = "primary";
                                            break;

                                        case "completed":
                                            $badge = "success";
                                            break;

                                        case "cancelled":
                                            $badge = "danger";
                                            break;
                                    }

                                    ?>

                                    <span
                                        class="badge badge-<?= $badge ?>">

                                        <?= ucfirst(
                                            $appointment["status"]
                                        ) ?>

                                    </span>

                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $appointment["reason"]
                                    ) ?>
                                </td>



                                <td>

                                    <a
                                        href="index.php?page=appointments&action=view&id=<?= $appointment["id"] ?>"
                                        class="btn btn-info btn-sm">

                                        View

                                    </a>

                                    <?php if (
                                        $appointment["status"] === "completed"
                                        &&
                                        ($appointment["has_prescription"] ?? false)
                                    ): ?>

                                        <a
                                            href="index.php?page=prescriptions&action=view&appointment_id=<?= $appointment["id"] ?>"
                                            class="btn btn-success btn-sm">

                                            Prescription

                                        </a>

                                    <?php endif; ?>

                                    <?php if (
                                        $appointment["status"] === "pending"
                                    ): ?>

                                        <form
                                            method="POST"
                                            action="index.php?page=appointments&action=cancel"
                                            style="display:inline;">

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= CSRF::generateToken() ?>">

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= $appointment["id"] ?>">

                                            <button
                                                type="submit"
                                                class="btn btn-danger btn-sm">

                                                Cancel

                                            </button>

                                        </form>

                                    <?php endif; ?>

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
require_once __DIR__
    . "/../partials/footer.php";
?>