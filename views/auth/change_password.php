<?php
$pageTitle = "Change Password";

require_once __DIR__ . "/../partials/header.php";
?>

<div class="login-page d-flex align-items-center justify-content-center bg-light" style="min-height: 100vh;">
    
    <div class="login-box" style="width: 420px;">
        <div class="login-logo font-weight-bold mb-3 text-center">
            <a href="#" class="text-dark"><b>Clinic</b>Desk</a>
        </div>

        <div class="card card-outline card-danger shadow-lg">
            <div class="card-body login-card-body rounded">
                <p class="login-box-msg font-weight-bold text-danger mb-1">
                    <i class="fas fa-shield-alt mr-1"></i> Mandatory Password Change
                </p>
                <p class="text-muted text-center small mb-3">For security purposes, you must change your temporary password before accessing the dashboard.</p>

                <?php if(!empty($errors)): ?>
                    <div class="alert alert-danger p-2 small mb-3">
                        <ul class="mb-0 pl-3">
                            <?php foreach($errors as $err) echo "<li>$err</li>"; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?page=auth&action=updatePassword">

                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

                    <label class="small font-weight-bold text-secondary mb-1">Current Password</label>
                    <div class="input-group mb-3">
                        <input type="password" name="current_password" class="form-control" placeholder="Current Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-key text-muted"></span>
                            </div>
                        </div>
                    </div>

                    <label class="small font-weight-bold text-secondary mb-1">New Password</label>
                    <div class="input-group mb-1">
                        <input type="password" name="new_password" class="form-control" placeholder="New Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock text-muted"></span>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted d-block mb-3" style="font-size: 11px; line-height: 1.3;">
                        * Must be at least 8 characters, containing uppercase letters (A-Z), lowercase letters (a-z), and digits (0-9).
                    </small>

                    <label class="small font-weight-bold text-secondary mb-1">Confirm New Password</label>
                    <div class="input-group mb-4">
                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm New Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock-open text-muted"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-danger btn-block font-weight-bold py-2 shadow-sm" type="submit">
                                <i class="fas fa-check-circle mr-1"></i> Update Password & Login
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>

</body>
</html>