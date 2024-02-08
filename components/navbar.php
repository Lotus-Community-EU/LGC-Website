<nav class="navbar navbar-expand-lg navbar-dark mb-5">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">
            <?= $config['lgc_text'];?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link nav-check" id="index" href="/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Link</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php if($user->hasPermission('admin_admin_tab')) { ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle nav-check" id="admin" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Administration
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-lg-end">
                            <?php
                                // Functions::AddAdminTabLink($link, $icon, $permission, $text)
                                Functions::AddAdminTabLink('user','/admin/user/list','fa-solid fa-users','admin_user_list', Functions::Translation('nav.admin.user_list'));
                                Functions::AddAdminTabLink('ranks','/admin/ranks/list','fa-solid fa-user-gear','admin_rank_management', Functions::Translation('nav.admin.rank_management'));
                                Functions::AddAdminTabLink('translation','/admin/translation/list','fa-solid fa-language','admin_translation_list', Functions::Translation('nav.admin.translation_management'));
                                Functions::AddAdminTabLink('website_settings','/admin/website_settings','fa-solid fa-wrench','admin_website_settings', Functions::Translation('nav.admin.website_settings'));
                                Functions::AddAdminTabLink('todo','/admin/todo/view','fa-solid fa-file-pen','admin_todo_access','To-Do System');
                            ?>
                        </ul>
                    </li>
                <?php } ?>
                <?php if($user->getID() == 0) { // User not loggedin ?>
                    <li class="nav-item">
                        <a class="nav-link nav-check" id="login" href="/login"><?= Functions::Translation('text.login');?></a>
                    </li>
                <?php } else { // User loggedin ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle nav-check" id="profile" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= $user->getUsername();?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-lg-end">
                            <a class="dropdown-item nav-check" id="<?= $user->getID();?>" href="/profile/<?= $user->getID();?>"><?= Functions::Translation('text.profile');?></a>
                            <a class="dropdown-item nav-check" id="settings" href="/profile/settings"><?= Functions::Translation('text.account_settings');?></a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/logout"><?= Functions::Translation('text.logout');?></a>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>