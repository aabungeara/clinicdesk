<?php

$pageTitle = "Login";

require_once __DIR__ . "/../partials/header.php";
?>

<div class="login-page d-flex align-items-center justify-content-center bg-light" style="min-height: 100vh;">
    
    <div class="login-box">
        <div class="login-logo font-weight-bold mb-3 text-center">
            <a href="#" class="text-dark"><b>Clinic</b>Desk</a>
        </div>

        <div class="card card-outline card-primary shadow-lg">
            <div class="card-body login-card-body rounded">
                <p class="login-box-msg text-muted">Sign in to start your session</p>

                <form method="POST" action="index.php?page=auth&action=login">

                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope text-muted"></span>
                            </div>
                        </div>
                    </div>

                    <div class="input-group mb-4">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock text-muted"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-primary btn-block font-weight-bold py-2 shadow-sm" type="submit">
                                <i class="fas fa-sign-in-alt mr-1"></i> Login
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>

<?php
?>
</body>
</html>