<?php if(!Functions::UserHasPermission('admin_rank_management')) { // User has no permission to edit Users
    $_SESSION['error_title'] = 'Permissions - Edit Ranks';
    $_SESSION['error_message'] = 'You don\'t have permissions to edit ranks!';
    header("Location: /admin/ranks/list");
    exit;
}
$rank_id = $GET[3];
$all_ranks = Functions::GetAllRanks();
$rank = $all_ranks[$rank_id];
$rank_permissions = Functions::GetRankPermissions($rank['id']);
$rank_names = Functions::GetRankComments();
$all_permissions = Functions::GetAllPermissions();

$csrf_token = Functions::CreateCSRFToken();
?>
<div class="container w-50 mb-5">
    <div class="d-flex justify-content-between">
        <div>
            <p><?= Functions::Translation('edit_rank_header', ['rank_name'], [$rank['rank_name']]);?></p>
        </div>
        <div>
            <a href="" class="btn btn-sm btn-danger mb-2 mb-md-0" data-bs-toggle="modal" data-bs-target="#delete_rank"><?= Functions::Translation('delete_rank');?></a>
        </div>
    </div>
    <form action="/admin/rank_edit/<?= $GET['3'];?>" method="POST">

        <hr>
        <h5><?= Functions::Translation('edit_rank.main_header');?></h5>
        <hr>

        <div class="form-group">
            <?php $rank_name = Functions::Translation('rank_edit.rank_name');?>
            <label for="rank_name"><?= $rank_name;?></label>
            <input type="text" name="rank_name" class="form-control" id="rank_name" placeholder="<?= $rank_name;?>" value="<?= $rank['rank_name'];?>">
        </div>
        <div class="form-group mt-3">
            <?php $rank_short = Functions::Translation('rank_edit.rank_short');?>
            <label for="rank_short"><?= $rank_short;?></label>
            <input type="text" name="rank_short" class="form-control" id="rank_short" placeholder="<?= $rank_short;?>" value="<?= $rank['rank_short'];?>">
        </div>
        <div class="form-group mt-3">
            <?php $rank_colour = Functions::Translation('rank_edit.rank_colour');?>
            <label for="rank_colour"><?= $rank_colour;?></label>
            <input type="color" name="rank_colour" class="form-control" id="rank_colour" placeholder="<?= $rank_colour;?>" value="<?= $rank['rank_colour'];?>">
        </div>

        <hr>
        <h5><?= Functions::Translation('edit_rank.permissions_header');?></h5>
        <hr>

        <div class="row">
            <div class="col-12 col-md-6">

                <div class="form-check mt-3">
                    <?php $rank_is_staff = Functions::Translation('rank_edit.is_staff');?>
                    <input type="checkbox" name="rank_is_staff" class="form-check-input" id="rank_is_staff" value="rank_is_staff" <?= $rank['is_staff'] == 1 ? 'checked' : '';?>>
                    <label class="form-check-label" for="rank_is_staff"><?= $rank_is_staff;?></label>
                </div>

                </div>
                <div class="col-12 col-md-6">

                <div class="form-check mt-3">
                    <?php $rank_is_upper_staff = Functions::Translation('rank_edit.is_upper_staff');?>
                    <input type="checkbox" name="rank_is_upper_staff" class="form-check-input" id="rank_is_upper_staff" value="rank_is_upper_staff" <?= $rank['is_upperstaff'] == 1 ? 'checked' : '';?>>
                    <label class="form-check-label" for="rank_is_upper_staff"><?= $rank_is_upper_staff;?></label>
                </div>

            </div>
        </div>

        <?php
            foreach($all_permissions as $permission) {
                ?>
                <div class="form-check mt-3">
                    <input type="checkbox" name="<?= $permission;?>" class="form-check-input" id="<?= $permission;?>" value="<?= $permission;?>" <?= $rank_permissions[$permission] == 1 ? 'checked' : '';?>>
                    <label class="form-check-label" for="<?= $permission;?>"><?= $rank_names[$permission].' (<b>'.$permission.'</b>)';?></label>
                </div>
                <?php
            }
        ?>

        <?php Functions::AddCSRFCheck($csrf_token);?>
        <input type="submit" class="btn btn-success w-100 mt-3" value="<?= Functions::$translations['edit'];?>">
    </form>
</div>

<!-- Delete Rank -->
<div class="modal" id="delete_rank" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Functions::Translation('rank_edit.delete_title', ['rank_name'], [$rank['rank_name']]); ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <p><?= Functions::Translation('rank_edit.delete_text', ['rank_name','rank_short'], [$rank['rank_name'], $rank['rank_short']]);?></p>
            </div>
            <div class="modal-footer">
                <form action="/admin/rank_delete/<?= $GET[3];?>" method="POST" class="">
                    <input type="submit" name="reset_password" class="btn btn-success" value="<?= Functions::Translation('rank_edit.delete_button');?>">
                    <?php Functions::AddCSRFCheck($csrf_token);?>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .form-check-label {
        text-decoration: none;
    }
</style>