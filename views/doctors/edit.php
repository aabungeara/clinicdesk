<?php

$pageTitle = "Edit Doctor";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

$doctor = $doctor ?? [];
$specializations = $specializations ?? [];

$selectedDays = explode(",", $doctor["available_days"] ?? "");
?>

<div class="content-wrapper">
    <section class="content p-3">
        <div class="card">
            <div class="card-header">
                <h3>Edit Doctor</h3>
            </div>

            <form method="POST" enctype="multipart/form-data" action="index.php?page=doctors&action=update">
                
                <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                <input type="hidden" name="id" value="<?= $doctor["id"] ?? '' ?>">

                <div class="card-body">
                    <div class="row">
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($doctor["doctor_name"] ?? $doctor["name"] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Specialization</label>
                                <select name="specialization_id" class="form-control" required>
                                    <?php foreach ($specializations as $spec): ?>
                                        <option value="<?= $spec["id"] ?>" <?= ($doctor["specialization_id"] ?? '') == $spec["id"] ? "selected" : "" ?>>
                                            <?= htmlspecialchars($spec["name"]) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row mt-3">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Consultation Fee</label>
                                <input type="number" step="0.01" name="consultation_fee" class="form-control" value="<?= htmlspecialchars($doctor["consultation_fee"] ?? '') ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Upload Photo</label>
                                <input type="file" name="photo" accept="image/png, image/jpeg, image/jpg" class="form-control">
                                <small class="text-muted">JPEG/PNG only, max 1 MB</small>
                            </div>
                        </div>

                    </div>

                    <hr>

                    <div class="form-group mt-3">
                        <label>Bio</label>
                        <textarea name="bio" rows="3" class="form-control" style="resize: vertical;"><?= htmlspecialchars($doctor["bio"] ?? "") ?></textarea>
                    </div>

                    <div class="form-group mt-4">
                        <label class="font-weight-bold d-block mb-2">Available Days</label>
                        <div class="d-flex flex-wrap pt-1">
                            <?php foreach (AVAILABLE_DAYS as $day): ?>
                                <div class="custom-control custom-checkbox mr-4 mb-2">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="day_<?= $day ?>" 
                                           name="available_days[]" 
                                           value="<?= $day ?>" 
                                           <?= in_array($day, $selectedDays) ? "checked" : "" ?>>
                                    <label class="custom-control-label" for="day_<?= $day ?>">
                                        <?= $day ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>

                <div class="card-footer d-flex ">
                    <button type="submit" class="btn btn-success px-4 mr-auto">Save Changes</button>
                    <a href="index.php?page=doctors" class="btn btn-secondary px-4">Cancel</a>
                </div>

            </form>
        </div>
    </section>
</div>

<?php 
require_once __DIR__ . "/../partials/footer.php"; 
?>