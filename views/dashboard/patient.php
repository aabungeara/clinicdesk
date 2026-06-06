<?php

Auth::requireRole("patient");

$pageTitle = "Patient Dashboard";

// تضمين المكونات العلوية الموحدة للمشروع (الـ Navbar المطور والسايدبار المشترك)
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

// تهيئة المتغيرات القادمة من الـ Controller لضمان استقرار الصفحة
$myAppointments = $myAppointments ?? [];
$patientStats = $patientStats ?? ['total' => 0, 'pending' => 0, 'completed' => 0];

?>

<div class="content-wrapper">

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold text-dark">Patient Dashboard</h1>
                </div>
                <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
                    <a href="index.php?page=appointments&action=create" class="btn btn-primary shadow-sm font-weight-bold">
                        <i class="fas fa-plus-circle mr-1"></i> Book New Appointment
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-lg-4 col-12">
                    <div class="small-box bg-info shadow-sm">
                        <div class="inner">
                            <h3><?= $patientStats['total'] ?? count($myAppointments) ?></h3>
                            <p>My Total Appointments</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-warning shadow-sm">
                        <div class="inner">
                            <h3><?= $patientStats['pending'] ?? 0 ?></h3>
                            <p>Pending Approvals</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-success shadow-sm">
                        <div class="inner">
                            <h3><?= $patientStats['completed'] ?? 0 ?></h3>
                            <p>Completed Visits</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-double"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card card-dark shadow-sm">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title font-weight-bold m-0">My Appointment History</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap valign-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Doctor Name</th>
                                        <th>Specialization</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th class="text-center" style="width: 140px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($myAppointments)): foreach ($myAppointments as $app): ?>
                                        <tr>
                                            <td class="font-weight-bold text-dark">
                                                <i class="fas fa-user-md text-muted mr-1"></i> 
                                                Dr. <?= htmlspecialchars($app['doctor_name']) ?>
                                            </td>
                                            <td>
                                                <span class="text-muted small">
                                                    <?= htmlspecialchars($app['specialization_name'] ?? 'General') ?>
                                                </span>
                                            </td>
                                            <td><i class="far fa-calendar-alt text-muted mr-1"></i> <?= $app['appt_date'] ?></td>
                                            <td>
                                                <i class="far fa-clock text-muted mr-1"></i> 
                                                <?= date('h:i A', strtotime($app['appt_time'])) ?>
                                            </td>
                                            <td>
                                                <?php 
                                                    $badgeColors = [
                                                        'completed' => 'success',
                                                        'pending'   => 'warning',
                                                        'confirmed' => 'info',
                                                        'cancelled' => 'danger'
                                                    ];
                                                    $currentStatus = strtolower($app['status']);
                                                    $color = $badgeColors[$currentStatus] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?= $color ?> px-2 py-1">
                                                    <?= ucfirst($app['status']) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="index.php?page=appointments&action=view&id=<?= $app['id'] ?>" class="btn btn-sm btn-outline-primary px-3 shadow-sm">
                                                    <i class="fas fa-eye mr-1"></i> View Details
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-5">
                                                <i class="fas fa-calendar-times fa-3x d-block mb-3 text-muted" style="opacity: 0.4;"></i>
                                                <h5>You haven't booked any appointments yet.</h5>
                                                <a href="index.php?page=appointments&action=create" class="btn btn-sm btn-primary mt-2">
                                                    Book Your First Appointment Now
                                                </a>
                                            </td>
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
// تضمين الفوتر المشترك للمشروع لإغلاق التاجات وتشغيل الـ JavaScript
require_once __DIR__ . "/../partials/footer.php";
?>