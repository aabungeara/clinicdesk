<?php

Auth::requireRole("doctor");

$pageTitle = "Doctor Dashboard";

// تضمين المكونات العلوية الموحدة للمشروع (الـ Navbar الافتراضي والسايدبار المشترك)
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

// تهيئة المتغيرات لضمان أمان الكود واستقراره
$todayAppointments = $todayAppointments ?? [];
$monthStats = $monthStats ?? [];

?>

<div class="content-wrapper">

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold text-dark">Doctor Dashboard</h1>
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
                            <h3><?= $monthStats['total'] ?? 0 ?></h3>
                            <p>Total Appointments (This Month)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-warning shadow-sm">
                        <div class="inner">
                            <h3><?= $monthStats['pending'] ?? 0 ?></h3>
                            <p>Pending Appointments</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-clock"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-success shadow-sm">
                        <div class="inner">
                            <h3><?= $monthStats['completed'] ?? 0 ?></h3>
                            <p>Completed Visits</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="mt-4 mb-2 font-weight-bold text-secondary">Today's Appointments By Status</h5>
            <div class="row">
                <?php 
                // توزيع دقيق للحالات الأربعة لليوم الحالي
                $todayStatuses = [
                    'pending'   => ['total' => 0, 'bg' => 'bg-warning', 'icon' => 'fas fa-clock'],
                    'confirmed' => ['total' => 0, 'bg' => 'bg-info',    'icon' => 'fas fa-check-circle'],
                    'completed' => ['total' => 0, 'bg' => 'bg-success', 'icon' => 'fas fa-calendar-check'],
                    'cancelled' => ['total' => 0, 'bg' => 'bg-danger',  'icon' => 'fas fa-times-circle']
                ];

                // حساب الأعداد الفعلية لمواعيد اليوم بناءً على المصفوفة الممررة
                if (!empty($todayAppointments)) {
                    foreach ($todayAppointments as $app) {
                        $statusName = strtolower($app['status']);
                        if (array_key_exists($statusName, $todayStatuses)) {
                            $todayStatuses[$statusName]['total']++;
                        }
                    }
                }

                foreach ($todayStatuses as $status => $data): 
                ?>
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box shadow-sm border">
                            <span class="info-box-icon <?= $data['bg'] ?> text-white shadow-sm">
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
                    <div class="card card-dark shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">Today's Appointments</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap valign-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Patient Name</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th class="text-center" style="width: 120px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($todayAppointments)): foreach ($todayAppointments as $app): ?>
                                        <tr>
                                            <td class="font-weight-bold text-dark"><?= htmlspecialchars($app['patient_name']) ?></td>
                                            <td><i class="far fa-calendar-alt text-muted mr-1"></i> <?= $app['appt_date'] ?></td>
                                            <td>
                                                <i class="far fa-clock text-muted mr-1"></i> 
                                                <?= date('h:i A', strtotime($app['appt_time'])) ?>
                                            </td>
                                            <td>
                                                <?php 
                                                    // مصفوفة لتحديد لون الـ Badge بشكل ديناميكي ونظيف
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
                                                <a href="index.php?page=appointments&action=view&id=<?= $app['id'] ?>" class="btn btn-sm btn-primary shadow-sm px-3">
                                                    <i class="fas fa-folder-open mr-1"></i> Manage
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="fas fa-notes-medical fa-2x d-block mb-2 text-muted" style="opacity: 0.5;"></i>
                                                No appointments registered for today.
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
// تضمين الفوتر المشترك للمشروع
require_once __DIR__ . "/../partials/footer.php";
?>