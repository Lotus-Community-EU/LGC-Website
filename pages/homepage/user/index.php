<?php
if(!isset($GET[1])) {
    header("Location: /user/".Functions::$user['id']);
    exit;
}
$user_id = $GET[1];
$user_data = Functions::GetUserData($user_id);
$user_ranks = Functions::GetUserRanks($user_id);
$all_ranks = Functions::GetAllRanks();
$languages = Functions::GetAllLanguages();

if(strlen($user_data['mc_uuid']) > 1)
$mc_username = json_decode(file_get_contents('https://mc-heads.net/minecraft/profile/'.$user_data['mc_uuid']))->name;
?>

<?php if(((Functions::IsStaff($user_data)) && Functions::UserHasPermission('admin_staff_management')) ||
                ((Functions::IsUpperStaff($user_data)) && Functions::UserHasPermission('admin_upperstaff_management')) || 
                (!Functions::IsStaff($user_data) && !Functions::IsUpperStaff($user_data) && Functions::UserHasPermission('admin_user_management')) || $user_id == Functions::$user['id']) { ?>
            <div class="row">
                <div class="container-fluid d-flex justify-content-end">
                    <?php if(((Functions::IsStaff($user_data)) && Functions::UserHasPermission('admin_staff_management')) ||
                        ((Functions::IsUpperStaff($user_data)) && Functions::UserHasPermission('admin_upperstaff_management')) || 
                        (!Functions::IsStaff($user_data) && !Functions::IsUpperStaff($user_data) && Functions::UserHasPermission('admin_user_management'))) {?>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Admin-Menu">
                                <i class="fa-solid fa-user-tie"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                                <li><a class="dropdown-item" href="/admin/user/edit/<?= $user_data['id'];?>"><?= Functions::Translation('edit_user');?></a></li>
                                <li><a class="dropdown-item" href="/admin/user/logs/<?= $user_data['id'];?>"><?= Functions::Translation('view_logs');?></a></li>
                            </ul>
                        </div>
                    <?php } ?>
                    <?php if($user_id == Functions::$user['id']) {?>
                        <div class="dropdown ms-2">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="User-Menu">
                                <i class="fa-solid fa-gear"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                                <li><a class="dropdown-item" href="/user/logs"><?= Functions::Translation('view_logs');?></a></li>
                                <li><a class="dropdown-item" href="/user/edit"><?= Functions::Translation('edit_profile');?></a></li>
                            </ul>
                        </div>
                    <?php } ?>
                </div>
            </div>
<?php } ?>

<div class="row mt-2">
    <div class="col-12 col-md-4 d-flex justify-content-center">

        <?php if(strlen($user_data['mc_uuid']) < 1) {
            ?><img src="https://mc-heads.net/avatar/MHF_Steve" width="180" height="180"><?php
        }
        else {
            ?><img src="https://mc-heads.net/avatar/<?= $user_data['mc_uuid'];?>/nohelm" height="180" width="180"><?php
        } ?>

    </div>
    <div class="col-12 col-md-8">
        <b><?= Functions::Translation('username');?>:</b> <?= $user_data['username'];?><br>
        <b>Main Rank:</b> <?= '<font color="'.$all_ranks[$user_ranks[0]]['rank_colour'].'">'.$all_ranks[$user_ranks[0]]['rank_name'].'</font>';?><br>
        <?= $user_ranks[1] != 0 ? '<b>Secondary Rank:</b> <font color="'.$all_ranks[$user_ranks[1]]['rank_colour'].'">'.$all_ranks[$user_ranks[1]]['rank_name'].'</font><br>' : '';?>
        <b><?= Functions::Translation('member_since');?></b> <?= date('d.m.Y - H:i', $user_data['created_at']);?><br>
        <b><?= Functions::Translation('language');?></b> <?= $languages[$user_data['language']]['language_name'];?><br>
        <?php if(Functions::IsStaff(Functions::$user) || Functions::IsUpperStaff(Functions::$user) || $user_data['show_mc_name'] == 1 || $user_data['id'] == Functions::$user['id']) {?>
        <b>Minecraft-Account:</b> <?= $user_data['mc_uuid'] > 1 ? 'Linked ('.$mc_username.')<br>' : 'Not Linked!';?>
        <?php } else {
            ?><b>Minecraft-Account:</b> <?= $user_data['mc_uuid'] > 1 ? 'Linked<br>' : 'Not Linked!';?><?php
        } ?>
        <br>
        <?= $user_data['bio'];?>
    </div>
</div>