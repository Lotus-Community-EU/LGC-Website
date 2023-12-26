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
    <form action="/admin/rank_add" method="POST">

        <hr>
        <h5><?= Functions::Translation('edit_add_rank.main_header');?></h5>
        <hr>

        <div class="form-group">
            <?php $rank_name = Functions::Translation('rank_edit.rank_name');?>
            <label for="rank_name"><?= $rank_name;?></label>
            <input type="text" name="rank_name" class="form-control" id="rank_name" placeholder="<?= $rank_name;?>">
        </div>
        <div class="form-group mt-3">
            <?php $rank_short = Functions::Translation('rank_edit.rank_short');?>
            <label for="rank_short"><?= $rank_short;?></label>
            <input type="text" name="rank_short" class="form-control" id="rank_short" placeholder="<?= $rank_short;?>">
        </div>
        <div class="form-group mt-3">
            <?php $rank_colour = Functions::Translation('rank_edit.rank_colour');?>
            <label for="rank_colour"><?= $rank_colour;?></label>
            <input type="color" name="rank_colour" class="form-control" id="rank_colour" placeholder="<?= $rank_colour;?>">
        </div>

        <hr>
        <h5><?= Functions::Translation('edit_add_rank.permissions_header');?></h5>
        <hr>

        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-check mt-3">
                    <?php $rank_is_staff = Functions::Translation('rank_edit.is_staff');?>
                    <input type="checkbox" name="rank_is_staff" class="form-check-input" id="rank_is_staff" value="rank_is_staff">
                    <label class="form-check-label" for="rank_is_staff"><?= $rank_is_staff;?></label>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-check mt-3">
                    <?php $rank_is_upper_staff = Functions::Translation('rank_edit.is_upper_staff');?>
                    <input type="checkbox" name="rank_is_upper_staff" class="form-check-input" id="rank_is_upper_staff" value="rank_is_upper_staff">
                    <label class="form-check-label" for="rank_is_upper_staff"><?= $rank_is_upper_staff;?></label>
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