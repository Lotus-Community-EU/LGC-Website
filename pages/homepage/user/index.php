<?php
if(!isset($GET[1])) {
    header("Location: /user/".Functions::$user['id']);
    exit;
}
$user_id = $GET[1];
$user_data = Functions::GetUserData($user_id);
$user_ranks = Functions::GetUserRanks($user_id);
$all_ranks = Functions::GetAllRanks();
?>

<?php if((($all_ranks[$user_ranks[0]]['is_staff'] == 1 || $all_ranks[$user_ranks[1]]['is_staff'] == 1) && Functions::UserHasPermission('admin_staff_management')) ||
                (($all_ranks[$user_ranks[0]]['is_upperstaff'] == 1 || $all_ranks[$user_ranks[1]]['is_upperstaff'] == 1) && Functions::UserHasPermission('admin_upperstaff_management')) || 
                ($all_ranks[$user_ranks[0]]['is_staff'] == 0 && $all_ranks[$user_ranks[1]]['is_upperstaff'] == 0 && Functions::UserHasPermission('admin_user_management')) || $user_id == Functions::$user['id']) { ?>
            <div class="row">
                <div class="container-fluid d-flex justify-content-end">
                    <?php if((($all_ranks[$user_ranks[0]]['is_staff'] == 1 || $all_ranks[$user_ranks[1]]['is_staff'] == 1) && Functions::UserHasPermission('admin_staff_management')) ||
                        (($all_ranks[$user_ranks[0]]['is_upperstaff'] == 1 || $all_ranks[$user_ranks[1]]['is_upperstaff'] == 1) && Functions::UserHasPermission('admin_upperstaff_management')) || 
                        ($all_ranks[$user_ranks[0]]['is_staff'] == 0 && $all_ranks[$user_ranks[1]]['is_upperstaff'] == 0 && Functions::UserHasPermission('admin_user_management'))) {?>
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
                                <li><a class="dropdown-item" href="/user/edit"><?= Functions::Translation('edit_profile');?></a></li>
                            </ul>
                        </div>
                    <?php } ?>
                </div>
            </div>
<?php } ?>
<div class="row mt-2">
    <div class="container-fluid">Profil</div>
</div>