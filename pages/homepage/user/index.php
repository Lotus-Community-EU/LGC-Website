<?php
if(!isset($GET[1])) {
    header("Location: /user/".$user->getID());
    exit;
}
if(strtolower($GET[1]) == 'settings') {
    include('settings.php');
}
elseif(strtolower($GET[1]) == 'password') {
    include('password.php');
}
else {
    $user_id = $GET[1];

    $user_data = new User($user_id);
    $main_rank = new Rank($user_data->getMainRank());
    $secondary_rank = new Rank($user_data->getSecondaryRank());

    $all_languages = Functions::GetAllLanguages();

    if(strlen($user_data->getMCUUID()) > 1) {
        $mc_username = $user->getMCName();
    }
    else {
        $mc_username = 'Unset';
    }
    ?>

    <?php if((($user_data->getIsStaff()) && $user->hasPermission('admin_staff_management')) ||
                    (($user_data->getIsUpperStaff()) && $user->hasPermission('admin_upperstaff_management')) || 
                    (!$user_data->getIsStaff() && !$user_data->getIsUpperStaff() && $user->hasPermission('admin_user_management')) || $user_id == $user->getID()) { ?>
                <div class="row">
                    <div class="container-fluid d-flex justify-content-end">
                        <?php if((($user_data->getIsStaff()) && $user->hasPermission('admin_staff_management')) ||
                            (($user_data->getIsUpperStaff()) && $user->hasPermission('admin_upperstaff_management')) || 
                            (!$user_data->getIsStaff() && !$user_data->getIsUpperStaff() && $user->hasPermission('admin_user_management'))) {?>
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Admin-Menu">
                                    Admin
                                </button>
                                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                                    <li><a class="dropdown-item" href="/admin/user/edit/<?= $user_data->getID();?>"><?= Functions::Translation('text.edit_user');?></a></li>
                                    <li><a class="dropdown-item" href="/admin/user/logs/<?= $user_data->getID();?>"><?= Functions::Translation('text.view_logs');?></a></li>
                                </ul>
                            </div>
                        <?php } ?>
                        <?php if($user_id == $user->getID()) {?>
                            <div class="dropdown ms-2">
                                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="User-Menu">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                                    <li><a class="dropdown-item" href="/user/settings"><?= Functions::Translation('text.account_settings');?></a></li>
                                </ul>
                            </div>
                        <?php } ?>
                    </div>
                </div>
    <?php } ?>

    <div class="row mt-2">
        <div class="col-12 col-md-4 d-flex justify-content-center">

            <?php if(strlen($user_data->getMCUUID()) < 1) {
                ?><img src="https://mc-heads.net/avatar/MHF_Steve" width="180" height="180"><?php
            }
            else {
                ?><img src="https://mc-heads.net/avatar/<?= $user_data->getMCUUID();?>/nohelm" height="180" width="180"><?php
            } ?>

        </div>
        <div class="col-12 col-md-8">
            <b><?= Functions::Translation('global.username');?>:</b> <?= $user_data->getUsername();?><br>
            <b>Main Rank:</b> <?= '<font color="'.$main_rank->getColour().'">'.$main_rank->getName().'</font>';?><br>
            <?= $user_data->getSecondaryRank() != 0 ? '<b>Secondary Rank:</b> <font color="'.$secondary_rank->getColour().'">'.$secondary_rank->getName().'</font><br>' : '';?>
            <b><?= Functions::Translation('text.member_since');?>:</b> <?= date('d.m.Y - H:i', $user_data->getCreatedAt());?><br>
            <b><?= Functions::Translation('global.language');?>:</b> <?= $all_languages[$user_data->getLanguage()]['language_name'];?><br>
            <?php if($user->getIsStaff() || $user->getIsUpperStaff() || $user_data->getShowMCName() == 1 || $user_data->getID() == $user->getID()) {?>
            <b>Minecraft-Account:</b> <?= $user_data->getMCUUID() > 1 ? 'Linked ('.$mc_username.')<br>' : 'Not Linked!';?>
            <?php } else {
                ?><b>Minecraft-Account:</b> <?= $user_data->getMCUUID() > 1 ? 'Linked<br>' : 'Not Linked!';?><?php
            } ?>
            <br><br>
            <?= $user_data->getBio();?>
        </div>
    </div>
<?php } ?>