<?php
$pageTitle = "Prescriptions";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

$userRole = Auth::role(); 
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-bold text-dark">
                        <i class="fas fa-file-prescription text-success mr-2"></i> Prescriptions Management
                    </h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content px-3">
        
        <div class="card card-outline card-success shadow">
            <div class="card-header bg-white py-3">
                <h3 class="card-title text-muted font-weight-bold" style="font-size: 1.1rem;">
                    <i class="fas fa-list mr-1"></i> Medical Prescriptions Records
                </h3>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered m-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center" style="width: 80px;">ID</th>
                                
                                <?php if ($userRole !== 'patient'): ?>
                                    <th>Patient Name</th>
                                <?php endif; ?>
                                
                                <?php if ($userRole !== 'doctor'): ?>
                                    <th>Doctor Name</th>
                                <?php endif; ?>
                                
                                <th>Diagnosis (التشخيص)</th>
                                <th>Created Date</th>
                                <th class="text-center" style="width: 220px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($prescriptions)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-folder-open fa-3x mb-3 text-gray-300"></i>
                                        <p class="mb-0 font-weight-bold">No prescription records found in your account.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($prescriptions as $prescription): ?>
                                    <tr>
                                        <td class="text-center font-weight-bold text-secondary">
                                            #<?= $prescription['id'] ?>
                                        </td>
                                        
                                        <?php if ($userRole !== 'patient'): ?>
                                            <td>
                                                <i class="fas fa-user-injured text-muted mr-1"></i>
                                                <?= htmlspecialchars($prescription['patient_name'] ?? 'N/A') ?>
                                            </td>
                                        <?php endif; ?>
                                        
                                        <?php if ($userRole !== 'doctor'): ?>
                                            <td>
                                                <i class="fas fa-user-md text-muted mr-1"></i>
                                                Dr. <?= htmlspecialchars($prescription['doctor_name'] ?? 'N/A') ?>
                                            </td>
                                        <?php endif; ?>
                                        
                                        <td>
                                            <span class="d-inline-block text-truncate" style="max-width: 280px;" title="<?= htmlspecialchars($prescription['diagnosis']) ?>">
                                                <?= htmlspecialchars($prescription['diagnosis']) ?>
                                            </span>
                                        </td>
                                        
                                        <td>
                                            <i class="far fa-calendar-alt text-muted mr-1"></i>
                                            <?= date('Y-m-d', strtotime($prescription['created_at'])) ?>
                                        </td>
                                                <a href="index.php?page=prescriptions&action=view&id=<?= $prescription['id'] ?>" 
                                                   class="btn btn-sm btn-info text-white" title="View Details">
                                                    <i class="fas fa-eye mr-1"></i> View
                                                </a>
                                                
                                                <?php if (!empty($prescription['file_path']) && $userRole !== 'admin'): ?>
                                                    <a href="index.php?page=prescriptions&action=download&id=<?= $prescription['id'] ?>" 
                                                       class="btn btn-sm btn-success" target="_blank" title="Download PDF File">
                                                        <i class="fas fa-file-pdf mr-1"></i> PDF
                                                    </a>
                                                <?php elseif ($userRole === 'admin'): ?>
                                                    <button class="btn btn-sm btn-secondary disabled" title="Restricted for Admin" disabled>
                                                        <i class="fas fa-lock mr-1"></i> PDF
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-light text-muted" title="No Attachment" disabled>
                                                        <i class="fas fa-times mr-1"></i> No File
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <?php if (isset($paginator) && !empty($prescriptions)): ?>
                <div class="card-footer bg-white clearfix">
                    <?= $paginator->renderLinks("index.php?page=prescriptions") ?>
                </div>
            <?php endif; ?>
        </div>
        
    </section>
</div>

<?php
require_once __DIR__ . "/../partials/footer.php";
?>