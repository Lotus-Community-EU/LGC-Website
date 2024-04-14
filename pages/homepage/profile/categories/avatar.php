<?php if($user->getCanChangeAvatar() == 1) {?>
<form action="/profile/avatar" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <input class="form-control" type="file" name="avatar" id="avatar">
    </div>
    <div class="row mt-2">
        <div class="col-12 col-md-6 mt-2">
            <input type="submit" name="submit" value="Upload" class="btn btn-success btn-sm w-100">
        </div>
        <?php if(strlen($user->getMCUUID()) > 1) {?>
        <div class="col-12 col-md-6 mt-md-2 mt-3">
            <input type="submit" name="use_mc_avatar" value="Use Minecraft-Avatar" class="btn btn-primary btn-sm w-100">
        </div>
        <?php } ?>

        <div class="col-12">
        <?php if($user->getAvatar() != 'none.png') {?>
            <input type="submit" name="remove_avatar" value="Remove Avatar" class="btn btn-danger btn-sm w-100 mt-3">
        <?php } ?>
        </div>
    </div>
    <?php Functions::AddCSRFCheck('profile_settings', $csrf_token);?>
</form>
<?php } else {
    echo 'You can not change your Avatar!';
}