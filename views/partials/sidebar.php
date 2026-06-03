<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <a href="index.php?page=dashboard"
        class="brand-link">

        <span class="brand-text font-weight-light">
            ClinicDesk
        </span>

    </a>

    <div class="sidebar">

        <nav class="mt-2">

            <ul class="nav nav-pills nav-sidebar flex-column">

                <li class="nav-item">

                    <a href="index.php?page=dashboard"
                        class="nav-link">

                        <i class="nav-icon fas fa-home"></i>

                        <p>Dashboard</p>

                    </a>

                </li>
                <?php if (
                    Auth::check() &&
                    Auth::role() === "admin"
                ): ?>

                    <li class="nav-item">

                        <a
                            href="index.php?page=users"
                            class="nav-link">

                            <i class="nav-icon fas fa-users"></i>
                            <p>Users</p>

                        </a>

                    </li>

                <?php endif; ?>
                <?php if (
                    Auth::check() &&
                    Auth::role() === "admin"
                ): ?>
                    <li class="nav-item">

                        <a
                            href="index.php?page=doctors"
                            class="nav-link">

                            <i class="nav-icon fas fa-user-md"></i>

                            <p>Doctors</p>

                        </a>

                    </li>
                    <li class="nav-item">

                        <a
                            href="index.php?page=specializations"
                            class="nav-link">

                            <i class="nav-icon fas fa-stethoscope"></i>

                            <p>Specializations</p>

                        </a>
                    </li>
                <?php endif; ?>

            </ul>

        </nav>

    </div>

</aside>