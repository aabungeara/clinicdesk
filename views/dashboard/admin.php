<?php

Auth::requireRole("admin");

$pageTitle = "Admin Dashboard";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

$todayAppointmentsCount = $todayAppointmentsCount ?? 0;
$weekStats = $weekStats ?? [];
$recentAppointments = $recentAppointments ?? [];
$rolesCount = $rolesCount ?? [];

?>

<div class="content-wrapper">

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Admin Dashboard</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>
                                <?php
                                // جلب إجمالي المدراء مثلاً من المصفوفة الممررة
                                $admins = array_filter($rolesCount, fn($r) => $r['role'] === 'admin');
                                echo !empty($admins) ? reset($admins)['total'] : 0;
                                ?>
                            </h3>
                            <p>Total Admins</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-shield"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>
                                <?php
                                $doctors = array_filter($rolesCount, fn($r) => $r['role'] === 'doctor');
                                echo !empty($doctors) ? reset($doctors)['total'] : 0;
                                ?>
                            </h3>
                            <p>Total Doctors</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-md"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>
                                <?php
                                $patients = array_filter($rolesCount, fn($r) => $r['role'] === 'patient');
                                echo !empty($patients) ? reset($patients)['total'] : 0;
                                ?>
                            </h3>
                            <p>Total Patients</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-injured"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $todayAppointmentsCount ?></h3>
                            <p>Appointments Today</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-check"></i></div>
                    </div>
                </div>
            </div>

            <h5 class="mt-4 mb-2">Week's Appointments By Status</h5>
            <div class="row">
                <?php

                $allStatuses = [
                    'pending'   => ['total' => 0, 'bg' => 'bg-warning', 'icon' => 'fas fa-clock'],
                    'confirmed' => ['total' => 0, 'bg' => 'bg-info',    'icon' => 'fas fa-check-circle'],
                    'completed' => ['total' => 0, 'bg' => 'bg-success', 'icon' => 'fas fa-calendar-check'],
                    'cancelled' => ['total' => 0, 'bg' => 'bg-danger',  'icon' => 'fas fa-times-circle']
                ];


                if (!empty($weekStats)) {
                    foreach ($weekStats as $stat) {
                        $statusName = strtolower($stat['status']);
                        if (array_key_exists($statusName, $allStatuses)) {
                            $allStatuses[$statusName]['total'] = $stat['total'];
                        }
                    }
                }

                foreach ($allStatuses as $status => $data):
                ?>
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon <?= $data['bg'] ?> text-white">
                                <i class="<?= $data['icon'] ?>"></i>
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text text-muted text-uppercase small font-weight-bold"><?= $status ?></span>
                                <span class="info-box-number h3 font-weight-bold m-0" style="line-height: 1;"><?= $data['total'] ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card card-dark">
                        <div class="card-header">
                            <h3 class="card-title">Recent 5 Appointments</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Patient Name</th>
                                        <th>Doctor Name</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recentAppointments)): foreach ($recentAppointments as $app): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($app['patient_name']) ?></td>
                                                <td>Dr. <?= htmlspecialchars($app['doctor_name']) ?></td>
                                                <td><?= $app['appt_date'] ?></td>
                                                <td><?= $app['appt_time'] ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $app['status'] === 'completed' ? 'success' : ($app['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                                        <?= ucfirst($app['status']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach;
                                    else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No appointments recorded yet.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php
require_once __DIR__ . "/../partials/footer.php";
?>