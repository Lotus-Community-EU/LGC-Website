<?php if(!$user->hasPermission('admin_user_management')) { // User has no permission to edit Users
    $_SESSION['error_title'] = 'Permissions - Edit User';
    $_SESSION['error_message'] = 'You don\'t have permissions to edit Users!';
    header("Location: /admin/user/list");
    exit;
}
$user_id = $GET['3'];
$user_data = new User($user_id);

$main_rank = new Rank($user_data->getMainRank());
$secondary_rank = new Rank($user_data->getSecondaryRank());

if($user_data == null) { // User doesn't exist
    $_SESSION['error_title'] = 'Edit User - User does not exist';
    $_SESSION['error_message'] = 'The User you tried to edit doesn\'t exist!';
    header("Location: /admin/user/list");
    exit;
}

if(($main_rank->getIsStaff() == 1 || $secondary_rank->getIsStaff() == 1) && !$user->hasPermission('admin_staff_management')) {
    $_SESSION['error_title'] = 'Permissions - Edit Staff';
    $_SESSION['error_message'] = Functions::Translation('text.error.edit_staff');
    header("Location: /admin/user/list");
    exit;
}
if(($main_rank->getIsUpperStaff() == 1 || $secondary_rank->getIsUpperStaff() == 1) && !$user->hasPermission('admin_upperstaff_management')) {
    $_SESSION['error_title'] = 'Permissions - Edit Upper-Staff';
    $_SESSION['error_message'] = Functions::Translation('text.error.edit_upperstaff');
    header("Location: /admin/user/list");
    exit;
}
$all_languages = Functions::GetAllLanguages();

$all_ranks = Rank::getAllRanks();

$csrf_token = Functions::CreateCSRFToken('admin_edit_user');
?>

<div class="container w-50 mb-5">
    <div class="d-flex justify-content-between">
        <div>
            <p><?= Functions::Translation('text.edit_user.header', ['username'], [$user_data->getUsername()]);?></p>
        </div>
        <div>
            <a href="" class="btn btn-sm btn-danger mb-2 mb-md-0" data-bs-toggle="modal" data-bs-target="#reset_password"><?= Functions::Translation('text.reset_password.button');?></a>
        </div>
    </div>
    <form action="/admin/user/edit" method="POST">
        <div class="form-group">
            <label for="username"><?= Functions::Translation('global.username');?></label>
            <input type="text" name="username" id="username" class="form-control" value="<?= $user_data->getUsername();?>">
        </div>
        <div class="form-group mt-3">
            <label for="created_at"><?= Functions::Translation('text.account_created_at');?> (UTC)</label>
            <input type="text" id="created_at" class="form-control" value="<?= date('d.m.Y', $user_data->getCreatedAt());?>" disabled>
        </div>

        <div class="form-group mt-3">
            <label for="language"><?= Functions::Translation('global.language');?></label>
            <select class="form-control" id="language" name="language">
                <?php foreach($all_languages as $language) { ?>
                    <option value="<?= $language['language_code'];?>" <?= $language['language_code'] == $user_data->getLanguage() ? 'selected':'';?>><?= $language['language_name'];?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group mt-3">
            <div class="row">
                <div class="col-6 col-lg-6 mt-2">
                    <label for="can_change_avatar"><?= Functions::Translation('text.can_change_avatar');?></label>
                </div>
                <div class="col-6 col-lg-6 mt-2 text-end">
                    <!--<input type="submit" name="remove_avatar" value="Remove Avatar" class="btn btn-danger btn-sm">-->
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="remove_avatar" name="remove_avatar" value="1">
                        <label class="form-check-label" for="remove_avatar">Remove Avatar</label>
                    </div>
                </div>
            </div>
            <select name="can_change_avatar" id="can_change_avatar" class="form-control">
                <option value="1" <?= $user_data->getCanChangeAvatar() == 1 ? 'selected' : '';?>><?= Functions::Translation('global.yes');?></option>
                <option value="0" <?= $user_data->getCanChangeAvatar() == 0 ? 'selected' : '';?>><?= Functions::Translation('global.no');?></option>
            </select>
        </div>

        <?php if($user->hasPermission('admin_upperstaff_management')) { ?>
            <div class="form-group mt-3">
            <label for="main_rank">Main Rank</label>
            <select name="main_rank" id="main_rank" class="form-control">
                <?php foreach($all_ranks as $rank) { ?>
                    <option value="<?= $rank['id'];?>" <?= $rank['id'] == $user_data->getMainRank() ? 'selected' : '';?>><?= $rank['name'];?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group mt-3">
            <label for="secondary_rank">Secondary Rank</label>
            <select name="secondary_rank" id="secondary_rank" class="form-control">
                <option value="0" <?= 0 == $user_data->getSecondaryRank() ? 'selected' : '';?>>None</option>
                <?php foreach($all_ranks as $rank) { ?>
                    <option value="<?= $rank['id'];?>" <?= $rank['id'] == $user_data->getSecondaryRank() ? 'selected' : '';?>><?= $rank['name'];?></option>
                <?php } ?>
            </select>
        </div>
        <?php } ?>

        <div class="form-group mt-3">
            <label for="bio">Bio</label>
            <textarea id="bio" name="bio"><?= $user_data->getBio();?></textarea>
            <script>
                $(document).ready(function() {
                    $('#bio').summernote({
                        minHeight: 200,
                        theme: 'bs4-dark'
                    });
                });
            </script>
        </div>

        <?php Functions::AddCSRFCheck('admin_edit_user', $csrf_token); $_SESSION['user_id'] = $user_data->getID();?>
        <input type="hidden" name="user_id" value="<?= $user_data->getID();?>">
        <input type="submit" class="btn btn-success w-100 mt-3" value="<?= Functions::Translation('global.edit');?>">
    </form>
</div>

<!-- E-Mail Reset -->
<div class="modal" id="reset_password" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Functions::Translation('text.reset_password.title'); ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <p><?= Functions::Translation('text.reset_password.text', ['username'], [$user_data->getUsername()]);?></p>
            </div>
            <div class="modal-footer">
                <form action="/admin/user/resetpw" method="POST" class="">
                    <?php Functions::AddCSRFCheck('admin_edit_user', $csrf_token); $_SESSION['user_id'] = $user_data->getID();?>
                    <input type="hidden" name="user_id" value="<?= $user_data->getID();?>">
                    <input type="submit" name="reset_password" class="btn btn-success" value="<?= Functions::Translation('text.reset_password.button');?>">
                </form>
            </div>
        </div>
    </div>
</div>