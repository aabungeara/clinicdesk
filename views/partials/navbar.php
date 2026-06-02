<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    <ul class="navbar-nav">

        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#">
                <i class="fas fa-bars"></i>
            </a>
        </li>

    </ul>

    <ul class="navbar-nav ml-auto">

        <?php if (Auth::check()): ?>

            <li class="nav-item">

                <span class="nav-link">
                    <?= htmlspecialchars(
                        Auth::currentUser()["name"]
                    ) ?>
                </span>

            </li>

            <li class="nav-item">

                <form method="POST"
                    action="index.php?page=auth&action=logout"
                    class="d-inline">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= CSRF::generateToken() ?>">

                    <button
                        type="submit"
                        class="btn btn-link nav-link"
                        style="border:none;background:none;">

                        Logout

                    </button>

                </form>

            </li>

        <?php endif; ?>

    </ul>

</nav>