<?php 
$user_data = Functions::GetUserData(Functions::$user['id']);
$user_ranks = Functions::GetUserRanks(Functions::$user['id']);
$all_ranks = Functions::GetAllRanks();
$all_languages = Functions::GetAllLanguages();

?>

<div class="container w-50 mb-5">
    <div class="d-flex justify-content-between">
        <div>
            <p><?= Functions::Translation('edit_your_profile');?></p>
        </div>
    </div>
    <form action="/admin/user_edit/<?= $GET['3'];?>" method="POST" class="">
        <div class="form-group">
            <label for="username"><?= Functions::$translations['username'];?></label>
            <input type="text" name="username" id="username" class="form-control" value="<?= $user_data['username'];?>">
        </div>

        <div class="form-group mt-3">
            <label for="language"><?= Functions::$translations['language'];?></label>
            <select class="form-control" id="language" name="language">
                <?php foreach($all_languages as $language) { ?>
                    <option value="<?= $language['language_code'];?>" <?= $language['language_code'] == $user_data['language'] ? 'selected':'';?>><?= $language['language_name'];?></option>
                <?php } ?>
            </select>
        </div>

        <?php if(strlen($user_data['mc_uuid']) > 1) {
            $mc_username = json_decode(file_get_contents('https://mc-heads.net/minecraft/profile/'.$user_data['mc_uuid']))->name;
            ?>
                <div class="form-group mt-3">
                    <input type="submit" class="btn btn-sm btn-danger" value="Unlink Minecraft Account" name="unlink_mc"> (<?= $mc_username;?>)
                </div>

                <div class="form-group mt-3">
                    <label for="show_mc_name"><?= Functions::Translation('show_mc_name');?></label>
                    <select class="form-control" id="show_mc_name" name="show_mc_name">
                        <option value="0" <?= $user_data['show_mc_name'] == 0 ? 'selected' : '';?>><?= Functions::Translation('no');?></option>
                        <option value="1" <?= $user_data['show_mc_name'] == 1 ? 'selected' : '';?>><?= Functions::Translation('yes');?></option>
                    </select>
                </div>
            <?php
        } ?>

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

        <?php Functions::AddCSRFCheck();?>
        <input type="submit" class="btn btn-success w-100 mt-3" value="<?= Functions::$translations['edit'];?>">
    </form>
</div>