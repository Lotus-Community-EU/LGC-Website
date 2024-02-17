<?php if(!$user->hasPermission('admin_rank_management')) {
    $_SESSION['error_title'] = 'Permissions - Edit Ranks';
    $_SESSION['error_message'] = 'You don\'t have permissions to edit ranks!';
    header("Location: /admin/ranks/list");
    exit;
}
$rank_id = $GET[3];
$all_ranks = Functions::GetAllRanks();
$rank = new Rank($rank_id);
$rank_permissions = $rank->getPermissions();

$csrf_token = Functions::CreateCSRFToken('admin_ranks_edit');
?>
<div class="container col-12 col-lg-6 mb-5">
    <div class="d-flex justify-content-between">
        <div>
            <p><?= Functions::Translation('text.rank.edit.header', ['rank_name'], [$rank->getName()]);?></p>
        </div>
        <?php if($rank->getID() != 1 && $rank->getID() != 2) {?>
        <div>
            <a href="" class="btn btn-sm btn-danger mb-2 mb-md-0" data-bs-toggle="modal" data-bs-target="#delete_rank"><?= Functions::Translation('text.rank.delete.button');?></a>
        </div>
        <?php } ?>
    </div>
    <form action="/admin/ranks/edit" method="POST">

        <hr>
        <h5><?= Functions::Translation('text.rank.header.main');?></h5>
        <hr>

        <div class="form-group">
            <?php $rank_name = Functions::Translation('text.rank.rank_name');?>
            <label for="name"><?= $rank_name;?></label>
            <input type="text" name="name" class="form-control" id="name" placeholder="<?= $rank_name;?>" value="<?= $rank->getName();?>" maxlength="64">
        </div>
        <div class="form-group mt-3">
            <?php $rank_short = Functions::Translation('text.rank.rank_short');?>
            <label for="short"><?= $rank_short;?></label>
            <input type="text" name="short" class="form-control" id="short" placeholder="<?= $rank_short;?>" value="<?= $rank->getShort();?>" maxlength="6">
        </div>
        <div class="form-group mt-3">
            <?php $rank_colour = Functions::Translation('text.rank.rank_colour');?>
            <label for="colour"><?= $rank_colour;?></label>
            <input type="color" name="colour" class="form-control" id="colour" placeholder="<?= $rank_colour;?>" value="<?= $rank->getColour();?>">
        </div>
        <div class="form-group mt-3">
            <?php $rank_colour_ingame = Functions::Translation('text.rank.rank_colour.ingame');?>
            <label for="colour_ingame"><?= $rank_colour_ingame;?></label>
            <input type="text" name="colour_ingame" class="form-control" id="colour_ingame" placeholder="<?= $rank_colour_ingame;?>" value="<?= $rank->getColourIngame();?>" maxlength="5">
        </div>
        <div class="form-group mt-3">
            <?php $rank_ingame_id = Functions::Translation('text.rank.rank_ingame_id');?>
            <label for="ingame_id"><?= $rank_ingame_id;?></label>
            <input type="text" name="ingame_id" class="form-control" id="ingame_id" placeholder="<?= $rank_ingame_id;?>" value="<?= $rank->getIngameID();?>" maxlength="64">
        </div>
        <div class="form-group mt-3">
            <?php $rank_priority = Functions::Translation('text.rank.rank_priority');?>
            <label for="priority"><?= $rank_priority;?></label>
            <input type="text" pattern="[0-9]+" name="priority" class="form-control" id="priority" placeholder="<?= $rank_priority;?>" value="<?= $rank->getPriority();?>">
        </div>

        <hr>
        <h5><?= Functions::Translation('text.rank.header.permissions');?></h5>
        <hr>

        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-check mt-3">
                    <?php $rank_is_staff = Functions::Translation('text.rank.is_staff');?>
                    <input type="checkbox" name="is_staff" class="form-check-input" id="is_staff" value="is_staff" <?= $rank->getIsStaff() == 1 ? 'checked' : '';?>>
                    <label class="form-check-label" for="is_staff"><?= $rank_is_staff;?></label>
                </div>

                </div>
                <div class="col-12 col-md-6">

                <div class="form-check mt-3">
                    <?php $rank_is_upper_staff = Functions::Translation('text.rank.is_upperstaff');?>
                    <input type="checkbox" name="is_upper_staff" class="form-check-input" id="is_upper_staff" value="is_upper_staff" <?= $rank->getIsUpperStaff() == 1 ? 'checked' : '';?>>
                    <label class="form-check-label" for="is_upper_staff"><?= $rank_is_upper_staff;?></label>
                </div>

            </div>
        </div>

        <?php
            $all_permissions = Rank::getAllPermissions();
            foreach(Rank::$permission_categories as $cat_key => $category) {
                ?>
                <div class="row mt-5">
                    <h5 class="text-decoration-underline"><?= $category;?></h5>
                <?php
                foreach($all_permissions as $key => $permission) {
                    if($permission['permission_category'] == $cat_key) {
                        ?>
                        <div class="row">
                            <div class="col-8">
                                <label class="form-check-label" for="<?= $permission['permission_code'];?>"><?= $permission['permission_code'].'<br>(<b>'.$permission['permission_description'].'</b>)';?></label>
                            </div>
                            <div class="col-4">
                                <div class="form-check form-check-inline mt-3">
                                    <input type="radio" name="<?= $permission['permission_code'];?>" class="form-check-input" id="<?= $permission['permission_code'];?>" value="0" <?= !$rank->hasPermission($permission['permission_code']) ? 'checked' : '';?>>
                                    <label class="form-check-label" for="<?= $permission['permission_code'];?>">0</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" name="<?= $permission['permission_code'];?>" class="form-check-input" id="<?= $permission['permission_code'];?>" value="1" <?= $rank->hasPermission($permission['permission_code']) ? 'checked' : '';?>>
                                    <label class="form-check-label" for="<?= $permission['permission_code'];?>">1</label>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
                </div>
                <?php
            }
        ?>

        <?php Functions::AddCSRFCheck('admin_ranks_edit', $csrf_token); $_SESSION['rank_id'] = $rank->getID();?>
        <input type="hidden" name="rank_id" value="<?= $rank->getID();?>">
        <input type="submit" class="btn btn-success w-100 mt-3" value="<?= Functions::Translation('global.edit');?>">
    </form>
</div>

<?php if($rank->getID() != 1 && $rank->getID() != 2) {?>
<!-- Delete Rank -->
<div class="modal" id="delete_rank" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Functions::Translation('text.rank.delete.title', ['rank_name'], [$rank->getName()]); ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <p><?= Functions::Translation('text.rank.delete.text', ['rank_name','rank_short'], [$rank->getName(), $rank->getShort()]);?></p>
            </div>
            <div class="modal-footer">
                <form action="/admin/ranks/delete>" method="POST" class="">
                    <?php Functions::AddCSRFCheck('admin_ranks_edit', $csrf_token); $_SESSION['rank_id'] = $rank->getID();?>
                    <input type="hidden" name="rank_id" value="<?= $rank->getID();?>">
                    <input type="submit" name="reset_password" class="btn btn-success" value="<?= Functions::Translation('text.rank.delete.button');?>">
                </form>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<style>
    .form-check-label {
        text-decoration: none;
    }
</style>