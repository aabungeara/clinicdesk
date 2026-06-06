<?php
$pageTitle = "My Schedule";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

// دالة مساعدة لتحديد لون الشارة (Badge) بناءً على حالة الموعد
function getStatusBadge($status)
{
    $status = strtolower($status);
    switch ($status) {
        case 'pending':
            return '<span class="badge badge-warning p-2" style="font-size: 0.85rem;"><i class="fas fa-clock mr-1"></i> Pending</span>';
        case 'confirmed':
            return '<span class="badge badge-primary p-2" style="font-size: 0.85rem;"><i class="fas fa-check-circle mr-1"></i> Confirmed</span>';
        case 'completed':
            return '<span class="badge badge-success p-2" style="font-size: 0.85rem;"><i class="fas fa-check-double mr-1"></i> Completed</span>';
        case 'cancelled':
            return '<span class="badge badge-danger p-2" style="font-size: 0.85rem;"><i class="fas fa-times-circle mr-1"></i> Cancelled</span>';
        default:
            return '<span class="badge badge-secondary p-2" style="font-size: 0.85rem;">' . ucfirst($status) . '</span>';
    }
}
?>

<div class="content-wrapper">
    <section class="content p-3">

        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title text-bold text-warning">
                    <i class="fas fa-calendar-day mr-2"></i> Today's Appointments
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Patient</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th class="text-center" style="width: 150px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($todayAppointments)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No appointments scheduled for today.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($todayAppointments as $appointment): ?>
                                <tr>
                                    <td><?= htmlspecialchars($appointment["patient_name"]) ?></td>
                                    <td><i class="far fa-calendar-alt text-muted mr-1"></i> <?= $appointment["appt_date"] ?></td>
                                    <td><i class="far fa-clock text-muted mr-1"></i> <?= $appointment["appt_time"] ?></td>
                                    <td class="text-center"><?= getStatusBadge($appointment["status"]) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card card-outline card-primary mt-4">
            <div class="card-header">
                <h3 class="card-title text-bold text-primary">
                    <i class="fas fa-calendar-alt mr-2"></i> All Appointments
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Patient</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th class="text-center" style="width: 150px;">Status</th>
                            <th class="text-center" style="width: 180px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($appointments)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">No appointments found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($appointments as $appointment): ?>
                                <tr>
                                    <td><?= htmlspecialchars($appointment["patient_name"]) ?></td>
                                    <td><i class="far fa-calendar-alt text-muted mr-1"></i> <?= $appointment["appt_date"] ?></td>
                                    <td><i class="far fa-clock text-muted mr-1"></i> <?= $appointment["appt_time"] ?></td>
                                    <td class="text-center"><?= getStatusBadge($appointment["status"]) ?></td>
                                    <td class="text-center">
                                        <?php if ($appointment["status"] === "pending"): ?>
                                            <a href="index.php?page=appointments&action=confirm&id=<?= $appointment["id"] ?>"
                                                class="btn btn-primary btn-sm btn-block text-bold shadow-sm">
                                                <i class="fas fa-check mr-1"></i> Confirm
                                            </a>
                                        <?php endif; ?>

                                        <?php if ($appointment["status"] === "confirmed"): ?>
                                            <a href="index.php?page=appointments&action=complete&id=<?= $appointment["id"] ?>"
                                                class="btn btn-success btn-sm btn-block text-bold shadow-sm">
                                                <i class="fas fa-check-double mr-1"></i> Complete
                                            </a>
                                        <?php endif; ?>
                                        <?php if (
                                            $appointment["status"] === "completed"
                                            &&
                                            empty($appointment["prescription_id"])
                                        ): ?>

                                            <a
                                                href="index.php?page=prescriptions&action=create&appointment_id=<?= $appointment["id"] ?>"
                                                class="btn btn-success btn-sm">

                                                Add Prescription

                                            </a>

                                        <?php endif; ?>

                                        <?php if (
                                            !empty($appointment["prescription_id"])
                                        ): ?>
                                                
                                            <a
                                                href="index.php?page=prescriptions&action=view&id=<?= $appointment["prescription_id"] ?>"
                                                class="btn btn-info btn-sm">

                                                View Prescription

                                            </a>

                                        <?php endif; ?>

                                        <?php if ($appointment["status"] === "cancelled"): ?>

                                            <span class="text-muted">
                                                No actions available
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

    </section>
</div>

<?php
require_once __DIR__ . "/../partials/footer.php";
?>