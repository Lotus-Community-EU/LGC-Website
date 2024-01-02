<?php
$rank_names = Functions::GetRankComments();
$all_permissions = Functions::GetAllPermissions();
?>

<div class="container w-50 mb-5">
    <div class="d-flex justify-content-between">
        <div>
            <p><?= Functions::Translation('add_rank_header');?></p>
        </div>
    </div>
    <form action="/admin/ranks/add" method="POST">

        <hr>
        <h5><?= Functions::Translation('edit_add_rank.main_header');?></h5>
        <hr>

        <div class="form-group">
            <?php $name = Functions::Translation('rank_edit.rank_name');?>
            <label for="name"><?= $name;?></label>
            <input type="text" name="name" class="form-control" id="name" placeholder="<?= $name;?>">
        </div>
        <div class="form-group mt-3">
            <?php $short = Functions::Translation('rank_edit.rank_short');?>
            <label for="short"><?= $short;?></label>
            <input type="text" name="short" class="form-control" id="short" placeholder="<?= $short;?>">
        </div>
        <div class="form-group mt-3">
            <?php $colour = Functions::Translation('rank_edit.rank_colour');?>
            <label for="colour"><?= $colour;?></label>
            <input type="color" name="colour" class="form-control" id="colour" placeholder="<?= $colour;?>">
        </div>
        <div class="form-group mt-3">
            <?php $rank_colour_ingame = Functions::Translation('rank_edit.rank_colour_ingame');?>
            <label for="colour_ingame"><?= $rank_colour_ingame;?></label>
            <input type="text" name="colour_ingame" class="form-control" id="colour_ingame" placeholder="<?= $rank_colour_ingame;?>" maxlength="5">
        </div>
        <div class="form-group mt-3">
            <?php $rank_ingame_id = Functions::Translation('rank_edit.rank_ingame_id');?>
            <label for="ingame_id"><?= $rank_ingame_id;?></label>
            <input type="text" name="ingame_id" class="form-control" id="ingame_id" placeholder="<?= $rank_ingame_id;?>" maxlength="64">
        </div>
        <div class="form-group mt-3">
            <?php $rank_priority = Functions::Translation('rank_edit.rank_priority');?>
            <label for="priority"><?= $rank_priority;?></label>
            <input type="text" pattern="[0-9]+" name="priority" class="form-control" id="priority" placeholder="<?= $rank_priority;?>">
        </div>

        <hr>
        <h5><?= Functions::Translation('edit_add_rank.permissions_header');?></h5>
        <hr>

        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-check mt-3">
                    <?php $is_staff = Functions::Translation('rank_edit.is_staff');?>
                    <input type="checkbox" name="is_staff" class="form-check-input" id="is_staff" value="is_staff">
                    <label class="form-check-label" for="is_staff"><?= $is_staff;?></label>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-check mt-3">
                    <?php $is_upper_staff = Functions::Translation('rank_edit.is_upper_staff');?>
                    <input type="checkbox" name="is_upper_staff" class="form-check-input" id="is_upper_staff" value="is_upper_staff">
                    <label class="form-check-label" for="is_upper_staff"><?= $is_upper_staff;?></label>
                </div>
            </div>
        </div>

        <?php
            foreach($all_permissions as $permission) {
                ?>
                <div class="form-check mt-3">
                    <input type="checkbox" name="<?= $permission;?>" class="form-check-input" id="<?= $permission;?>" value="<?= $permission;?>">
                    <label class="form-check-label" for="<?= $permission;?>"><?= $rank_names[$permission].' (<b>'.$permission.'</b>)';?></label>
                </div>
                <?php
            }
        ?>


        <?php Functions::AddCSRFCheck();?>
        <input type="submit" class="btn btn-success w-100 mt-3" value="<?= Functions::Translation('add');?>">
    </form>
</div>