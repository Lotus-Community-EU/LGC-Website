<nav class="navbar navbar-expand-lg navbar-dark mb-5">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">
            <span style="color: var(--lotus_pink1);">Lotus</span> <span style="color: var(--lotus_green1);">Gaming</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Link</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php if(Functions::UserHasPermission('admin_admin_tab') == 1) { ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Administration
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-lg-end">
                            <?php
                                // Functions::AddAdminTabLink($link, $icon, $permission, $text)
                                Functions::AddAdminTabLink('/admin/user/list','fa-solid fa-users','admin_user_list', Functions::$translations['admin.user_list']);
                                //Functions::AddAdminTabLink('/admin/user_management','fa-solid fa-user-pen','admin_user_management', Functions::$translations['admin.user_management']);
                                Functions::AddAdminTabLink('/admin/roles/list','fa-solid fa-user-gear','admin_role_management', Functions::$translations['admin.role_management']);
                            ?>
                        </ul>
                    </li>
                <?php } ?>
                <?php if(Functions::$user['id'] == 0) { // User not loggedin ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/login"><?= Functions::$translations['login'];?></a>
                    </li>
                <?php } else { // User loggedin ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= Functions::$user['username'];?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-lg-end">
                            <a class="dropdown-item" href="/user/<?= Functions::$user['id'];?>"><?= Functions::$translations['profile'];?></a>
                            <a class="dropdown-item" href="/user/settings"><?= Functions::$translations['account_settings'];?></a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/logout"><?= Functions::$translations['logout'];?></a>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>