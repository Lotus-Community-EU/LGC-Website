<?php if(!Functions::UserHasPermission('admin_user_management')) { // User has no permission to edit Users
    $_SESSION['error_title'] = 'Permissions - Edit User';
    $_SESSION['error_message'] = 'You don\'t have permissions to edit Users!';
    header("Location: /admin/user/list");
    exit;
}
$user_id = $GET['3'];
$user_data = Functions::GetUserData($user_id);
$user_ranks = Functions::GetUserRanks($user_id);
if($user_data == '0') { // User doesn't exist
    $_SESSION['error_title'] = 'Edit User - User does not exist';
    $_SESSION['error_message'] = 'The User you tried to edit doesn\'t exist!';
    header("Location: /admin/user/list");
    exit;
}
$all_ranks = Functions::GetAllRanks();
if(($all_ranks[$user_ranks[0]]['is_staff'] == 1 || $all_ranks[$user_ranks[1]]['is_staff'] == 1) && !Functions::UserHasPermission('admin_staff_management')) {
    $_SESSION['error_title'] = 'Permissions - Edit Staff';
    $_SESSION['error_message'] = Functions::Translation('text.error.edit_staff');
    header("Location: /admin/user/list");
    exit;
}
if(($all_ranks[$user_ranks[0]]['is_upperstaff'] == 1 || $all_ranks[$user_ranks[1]]['is_upperstaff'] == 1) && !Functions::UserHasPermission('admin_upperstaff_management')) {
    $_SESSION['error_title'] = 'Permissions - Edit Upper-Staff';
    $_SESSION['error_message'] = Functions::Translation('text.error.edit_upperstaff');
    header("Location: /admin/user/list");
    exit;
}
$all_languages = Functions::GetAllLanguages();

$csrf_token = Functions::CreateCSRFToken();
?>

<div class="container w-50 mb-5">
    <div class="d-flex justify-content-between">
        <div>
            <p><?= Functions::Translation('text.edit_user.header', ['username'], [$user_data['username']]);?></p>
        </div>
        <div>
            <a href="" class="btn btn-sm btn-danger mb-2 mb-md-0" data-bs-toggle="modal" data-bs-target="#reset_password"><?= Functions::Translation('text.reset_password.button');?></a>
            <!--<a href="" class="btn btn-sm btn-warning">Reset Profile Picture</a>-->
        </div>
    </div>
    <form action="/admin/user/edit" method="POST">
        <div class="form-group">
            <label for="username"><?= Functions::Translation('global.username');?></label>
            <input type="text" name="username" id="username" class="form-control" value="<?= $user_data['username'];?>">
        </div>
        <div class="form-group mt-3">
            <label for="created_at"><?= Functions::Translation('text.account_created_at');?> (UTC)</label>
            <input type="text" id="created_at" class="form-control" value="<?= date('d.m.Y', $user_data['created_at']);?>" disabled>
        </div>

        <div class="form-group mt-3">
            <label for="language"><?= Functions::Translation('global.language');?></label>
            <select class="form-control" id="language" name="language">
                <?php foreach($all_languages as $language) { ?>
                    <option value="<?= $language['language_code'];?>" <?= $language['language_code'] == $user_data['language'] ? 'selected':'';?>><?= $language['language_name'];?></option>
                <?php } ?>
            </select>
        </div>

        <?php if(Functions::UserHasPermission('admin_upperstaff_management')) { ?>
            <div class="form-group mt-3">
            <label for="main_rank">Main Rank</label>
            <select name="main_rank" id="main_rank" class="form-control">
                <?php foreach($all_ranks as $main_rank) { ?>
                    <option value="<?= $main_rank['id'];?>" <?= $main_rank['id'] == $user_data['main_rank'] ? 'selected' : '';?>><?= $main_rank['name'];?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group mt-3">
            <label for="secondary_rank">Secondary Rank</label>
            <select name="secondary_rank" id="secondary_rank" class="form-control">
                <?php foreach($all_ranks as $secondary_rank) { ?>
                    <option value="<?= $secondary_rank['id'];?>" <?= $secondary_rank['id'] == $user_data['secondary_rank'] ? 'selected' : '';?>><?= $secondary_rank['name'];?></option>
                <?php } ?>
            </select>
        </div>
        <?php } ?>

        <div class="form-group mt-3">
            <label for="bio">Bio</label>
            <textarea id="bio" name="bio"><?= $user_data['bio'];?></textarea>
            <script>
                $(document).ready(function() {
                    $('#bio').summernote({
                        minHeight: 200
                    });
                });
            </script>
        </div>

        <?php Functions::AddCSRFCheck($csrf_token); $_SESSION['user_id'] = $user_data['id'];?>
        <input type="hidden" name="user_id" value="<?= $user_data['id'];?>">
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
            <p><?= Functions::Translation('text.reset_password.text', ['username'], [$user_data['username']]);?></p>
            </div>
            <div class="modal-footer">
                <form action="/admin/user/resetpw" method="POST" class="">
                    <?php Functions::AddCSRFCheck($csrf_token); $_SESSION['user_id'] = $user_data['id'];?>
                    <input type="hidden" name="user_id" value="<?= $user_data['id'];?>">
                    <input type="submit" name="reset_password" class="btn btn-success" value="<?= Functions::Translation('text.reset_password.button');?>">
                </form>
            </div>
        </div>
    </div>
</div>