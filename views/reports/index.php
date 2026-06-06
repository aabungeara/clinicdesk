<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0">Administrative Reports</h1>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <?php if(!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach($errors as $err) echo "<p class='mb-0'>$err</p>"; ?>
            </div>
        <?php endif; ?>

        <div class="card card-default">
            <div class="card-header bg-dark"><h3 class="card-title">Filter Criteria</h3></div>
            <div class="card-body">
                <form method="GET" action="index.php">
                    <input type="hidden" name="page" value="reports">
                    <input type="hidden" name="filter" value="1">
                    
                    <div class="row">
                        <div class="col-md-3">
                            <label>Start Date *</label>
                            <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label>End Date *</label>
                            <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label>Doctor (Optional)</label>
                            <select name="doctor_id" class="form-control">
                                <option value="">-- All Doctors --</option>
                                <?php foreach($doctors as $doc): ?>
                                    <option value="<?= $doc['id'] ?>" <?= (isset($_GET['doctor_id']) && $_GET['doctor_id'] == $doc['id']) ? 'selected' : '' ?>>
                                        Dr. <?= htmlspecialchars($doc['name']) ?> (<?= htmlspecialchars($doc['specialization_name']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Status (Optional)</label>
                            <select name="status" class="form-control">
                                <option value="">-- All Statuses --</option>
                                <option value="pending" <?= (($_GET['status'] ?? '') === 'pending') ? 'selected' : '' ?>>Pending</option>
                                <option value="confirmed" <?= (($_GET['status'] ?? '') === 'confirmed') ? 'selected' : '' ?>>Confirmed</option>
                                <option value="completed" <?= (($_GET['status'] ?? '') === 'completed') ? 'selected' : '' ?>>Completed</option>
                                <option value="cancelled" <?= (($_GET['status'] ?? '') === 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Generate Report</button>
                        <?php if(isset($_GET['filter']) && empty($errors) && !empty($reportData)): ?>
                            <a href="<?= $_SERVER['REQUEST_URI'] ?>&export=csv" class="btn btn-success"><i class="fas fa-file-csv"></i> Export to CSV</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <?php if(isset($_GET['filter']) && empty($errors)): ?>
            <div class="card mt-4">
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
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
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['patient_name']) ?></td>
                                    <td>Dr. <?= htmlspecialchars($row['doctor_name']) ?></td>
                                    <td><?= htmlspecialchars($row['specialization']) ?></td>
                                    <td><?= $row['appt_date'] ?></td>
                                    <td><?= $row['appt_time'] ?></td>
                                    <td><span class="badge badge-secondary"><?= ucfirst($row['status']) ?></span></td>
                                    <td><?= htmlspecialchars($row['reason'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="7" class="text-center">No matching records found for the selected range.</td></tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td colspan="2">Total Appointments Shown: <?= $totalShown ?></td>
                                <td colspan="5" class="text-right">
                                    Grouped Summary: 
                                    <span class="text-warning">Pending: <?= $statusCounts['pending'] ?></span> | 
                                    <span class="text-primary">Confirmed: <?= $statusCounts['confirmed'] ?></span> | 
                                    <span class="text-success">Completed: <?= $statusCounts['completed'] ?></span> | 
                                    <span class="text-danger">Cancelled: <?= $statusCounts['cancelled'] ?></span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>