<nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom shadow-sm">
    
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars text-secondary"></i>
            </a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">

        <?php if (Auth::check()): 
            // جلب اسم المستخدم الحالي ودوره ديناميكياً
            $currentUser = Auth::currentUser();
            $userName = isset($currentUser['name']) ? $currentUser['name'] : 'User';
            $userRole = isset($_SESSION['user_role']) ? ucfirst($_SESSION['user_role']) : 'Staff';
        ?>
            
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-toggle="dropdown">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-2 shadow-sm" style="width: 30px; height: 30px;">
                        <i class="fas fa-user-md small"></i>
                    </div>
                    <span class="d-none d-md-inline font-weight-bold text-dark mr-1">
                        <?= htmlspecialchars($userName) ?>
                    </span>
                    <i class="fas fa-angle-down text-muted small ml-1"></i>
                </a>
                
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right rounded border shadow-lg mt-2 animate-fade-in">
                    
                    <li class="user-header bg-dark d-flex flex-column align-items-center justify-content-center py-4 rounded-top">
                        <div class="bg-white text-dark rounded-circle d-flex align-items-center justify-content-center mb-2 shadow-sm font-weight-bold h4" style="width: 55px; height: 55px;">
                            <?= strtoupper(substr($userName, 0, 1)) ?>
                        </div>
                        <p class="text-white font-weight-bold m-0 h5">
                            <?= htmlspecialchars($userName) ?>
                        </p>
                        <small class="badge badge-info text-uppercase px-2 py-1 mt-1 font-weight-bold" style="letter-spacing: 1px;">
                            <?= htmlspecialchars($userRole) ?>
                        </small>
                    </li>
                    
                    <li class="user-footer bg-light p-3 d-flex justify-content-center rounded-bottom">
                        <form method="POST" action="index.php?page=auth&action=logout" class="w-100">
                            
                            <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                            
                            <button type="submit" class="btn btn-danger btn-block shadow-sm font-weight-bold py-2">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </li>

                </ul>
            </li>

        <?php endif; ?>

    </ul>
</nav>

<style>
/* تفعيل حركات انسيابية ناعمة لظهور القائمة المنسدلة */
.user-menu .dropdown-menu {
    transform-origin: top right;
}
.animate-fade-in {
    animation: fadeInDropdown 0.2s ease-out;
}
@keyframes fadeInDropdown {
    from { opacity: 0; transform: scale(0.95) translateY(-8px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
/* تنسيق إضافي لوسم الرأس متوافق مع كلاسات AdminLTE */
.user-header {
    text-align: center;
}
</style>