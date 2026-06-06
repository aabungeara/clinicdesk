<?php
require_once __DIR__ . "/../partials/header.php"; 
require_once __DIR__ . "/../partials/sidebar.php"; 
require_once __DIR__ . "/../partials/navbar.php"; 

// Fetch data passed from the Controller
$appointments = $appointments ?? [];
$stats = $stats ?? ['pending' => 0, 'confirmed' => 0, 'cancelled' => 0, 'completed' => 0];
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-calendar-alt text-primary mr-2"></i> Clinic Appointments Management</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $stats['pending'] ?></h3>
                            <p>Pending Appointments</p>
                        </div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $stats['confirmed'] ?></h3>
                            <p>Confirmed Appointments</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $stats['completed'] ?></h3>
                            <p>Completed Appointments</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-check"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $stats['cancelled'] ?></h3>
                            <p>Cancelled Appointments</p>
                        </div>
                        <div class="icon"><i class="fas fa-times-circle"></i></div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-secondary mb-4">
                <div class="card-header py-3 bg-light">
                    <form method="GET" action="index.php" class="form-inline d-flex justify-content-between w-100 flex-wrap">
                        <input type="hidden" name="page" value="appointments">
                        
                        <div class="btn-group mb-2 mb-sm-0" role="group">
                            <a href="index.php?page=appointments" class="btn btn-outline-secondary <?= !isset($_GET['status']) ? 'active' : '' ?>">All</a>
                            <a href="index.php?page=appointments&status=pending" class="btn btn-outline-warning <?= (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'active' : '' ?>">Pending</a>
                            <a href="index.php?page=appointments&status=confirmed" class="btn btn-outline-primary <?= (isset($_GET['status']) && $_GET['status'] == 'confirmed') ? 'active' : '' ?>">Confirmed</a>
                            <a href="index.php?page=appointments&status=completed" class="btn btn-outline-success <?= (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'active' : '' ?>">Completed</a>
                            <a href="index.php?page=appointments&status=cancelled" class="btn btn-outline-danger <?= (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'active' : '' ?>">Cancelled</a>
                        </div>

                        <div class="d-flex gap-2">
                            <input type="date" name="date" class="form-control mr-2" value="<?= $_GET['date'] ?? '' ?>">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-filter mr-1"></i> Filter</button>
                            <?php if(isset($_GET['date']) || isset($_GET['status'])): ?>
                                <a href="index.php?page=appointments" class="btn btn-secondary mr-2">Reset</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Appointment ID</th>
                                <th>Patient Name</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Reason / Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($appointments)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">No appointments recorded matching the current filter criteria.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($appointments as $appt): ?>
                                    <tr>
                                        <td><strong>#<?= htmlspecialchars($appt['id']) ?></strong></td>
                                        <td><?= htmlspecialchars($appt['patient_name'] ?? 'Unknown') ?></td>
                                        <td>Dr. <?= htmlspecialchars($appt['doctor_name'] ?? 'Unknown') ?></td>
                                        <td><?= htmlspecialchars($appt['appt_date']) ?></td>
                                        <td><?= date("g:i A", strtotime($appt['appt_time'])) ?></td>
                                        <td>
                                            <?php 
                                                $badgeBg = 'bg-secondary';
                                                $statusText = ucfirst($appt['status']);
                                                if ($appt['status'] == 'pending') { $badgeBg = 'bg-warning'; }
                                                elseif ($appt['status'] == 'confirmed') { $badgeBg = 'bg-info'; }
                                                elseif ($appt['status'] == 'completed') { $badgeBg = 'bg-success'; }
                                                elseif ($appt['status'] == 'cancelled') { $badgeBg = 'bg-danger'; }
                                            ?>
                                            <span class="badge <?= $badgeBg ?>"><?= $statusText ?></span>
                                        </td>
                                        <td><small class="text-muted"><?= htmlspecialchars($appt['reason'] ?? 'No reason specified') ?></small></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php if ($appt['status'] == 'pending'): ?>
                                                    <a href="index.php?page=appointments&action=approve&id=<?= $appt['id'] ?>" class="btn btn-sm btn-success" title="Approve Appointment"><i class="fas fa-check"></i></a>
                                                <?php endif; ?>
                                                
                                                <?php if ($appt['status'] != 'cancelled' && $appt['status'] != 'completed'): ?>
                                                    <a href="index.php?page=appointments&action=cancel&id=<?= $appt['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to cancel this appointment?')" title="Cancel Appointment"><i class="fas fa-times"></i></a>
                                                <?php endif; ?>
                                                
                                                <a href="index.php?page=appointments&action=view&id=<?= $appt['id'] ?>" class="btn btn-sm btn-info" title="Full Details"><i class="fas fa-eye"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
require_once __DIR__ . "/../partials/footer.php"; 
?>