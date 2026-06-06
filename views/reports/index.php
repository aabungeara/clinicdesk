<?php
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

require_once __DIR__ . "/../../models/DoctorModel.php";
$doctorModel = new DoctorModel();
$doctors = $doctorModel->getAllDoctors();
?>

<div class="content-wrapper">

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold text-dark">Administrative Reports</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            
            <?php if(!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5><i class="icon fas fa-ban"></i> Error</h5>
                    <?php foreach($errors as $err) echo "<p class='mb-0'>• $err</p>"; ?>
                </div>
            <?php endif; ?>

            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">Filter Criteria</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="index.php">
                        <input type="hidden" name="page" value="reports">
                        <input type="hidden" name="filter" value="1">
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Start Date *</label>
                                    <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">End Date *</label>
                                    <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Doctor (Optional)</label>
                                    <select name="doctor_id" class="form-control custom-select">
                                        <option value="">-- All Doctors --</option>
                                        <?php foreach($doctors as $doc): ?>
<option value="<?= $doc['id'] ?>" <?= (isset($_GET['doctor_id']) && $_GET['doctor_id'] == $doc['id']) ? 'selected' : '' ?>>
                                                Dr. <?= htmlspecialchars($doc['doctor_name'] ?? $doc['name'] ?? '') ?> (<?= htmlspecialchars($doc['specialization_name'] ?? '') ?>)                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Status (Optional)</label>
                                    <select name="status" class="form-control custom-select">
                                        <option value="">-- All Statuses --</option>
                                        <option value="pending" <?= (($_GET['status'] ?? '') === 'pending') ? 'selected' : '' ?>>Pending</option>
                                        <option value="confirmed" <?= (($_GET['status'] ?? '') === 'confirmed') ? 'selected' : '' ?>>Confirmed</option>
                                        <option value="completed" <?= (($_GET['status'] ?? '') === 'completed') ? 'selected' : '' ?>>Completed</option>
                                        <option value="cancelled" <?= (($_GET['status'] ?? '') === 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-search mr-1"></i> Generate Report
                            </button>
                            <?php if(isset($_GET['filter']) && empty($errors) && !empty($reportData)): ?>
                                <?php $csvUrl = "index.php?" . http_build_query(array_merge($_GET, ['export' => 'csv'])); ?>
                                <a href="<?= $csvUrl ?>" class="btn btn-success px-4 ml-2">
                                    <i class="fas fa-file-csv mr-1"></i> Export to CSV
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <?php if(isset($_GET['filter']) && empty($errors)): ?>
                <div class="card card-dark mt-4">
                    <div class="card-header">
                        <h3 class="card-title">Report Results</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Doctor Name</th>
                                    <th>Specialization</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalShown = 0;
                                $statusCounts = ['pending' => 0, 'confirmed' => 0, 'completed' => 0, 'cancelled' => 0];
                                
                                if(!empty($reportData)): 
                                    foreach($reportData as $row): 
                                        $totalShown++;
                                        if(array_key_exists($row['status'], $statusCounts)) {
                                            $statusCounts[$row['status']]++;
                                        }

                                        // ربط ألوان شارات الـ Badge لتطابق تصميم السيرفر تماماً (أصفر، أزرق، أخضر، أحمر)
                                        $badgeBg = 'secondary';
                                        if ($row['status'] === 'pending') $badgeBg = 'warning';
                                        elseif ($row['status'] === 'confirmed') $badgeBg = 'info';
                                        elseif ($row['status'] === 'completed') $badgeBg = 'success';
                                        elseif ($row['status'] === 'cancelled') $badgeBg = 'danger';
                                ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['patient_name']) ?></td>
                                            <td>Dr. <?= htmlspecialchars($row['doctor_name']) ?></td>
                                            <td><?= htmlspecialchars($row['specialization']) ?></td>
                                            <td><?= $row['appt_date'] ?></td>
                                            <td><?= $row['appt_time'] ?></td>
                                            <td>
                                                <span class="badge bg-<?= $badgeBg ?>">
                                                    <?= ucfirst($row['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($row['reason'] ?? '') ?></td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No matching records found for the selected range.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            
                            <?php if(!empty($reportData)): ?>
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td colspan="2" class="align-middle">Total Appointments: <?= $totalShown ?></td>
                                    <td colspan="5" class="text-right align-middle">
                                        <div class="d-inline-block">
                                            <span class="mr-2 text-muted">Summary:</span>
                                            <span class="badge bg-warning text-dark mx-1">Pending: <?= $statusCounts['pending'] ?></span>
                                            <span class="badge bg-info mx-1">Confirmed: <?= $statusCounts['confirmed'] ?></span>
                                            <span class="badge bg-success mx-1">Completed: <?= $statusCounts['completed'] ?></span>
                                            <span class="badge bg-danger mx-1">Cancelled: <?= $statusCounts['cancelled'] ?></span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php
require_once __DIR__ . "/../partials/footer.php";
?>