<?php
if(isset($GET[2]) && strtolower($GET[2]) == 'password') {
    include('password.php');
}
else {

    $all_languages = Functions::GetAllLanguages();

    $csrf_token = Functions::CreateCSRFToken();

    //$test = file_get_contents('https://mc-heads.net/avatar/d672499f-6506-4b57-85ee-a6fe435ca4f8/nohelm');
    //file_put_contents('assets/images/test.png', $test);

    ?>

    <div class="container w-50 mb-5">
        <div class="d-flex justify-content-between">
            <div>
                <p><?= Functions::Translation('text.edit_profile');?></p>
            </div>
            <div>
                <a href="/user/settings/password" class="btn btn-sm btn-success mb-2 mb-md-0"><?= Functions::Translation('text.change_password');?></a>
                <!--<a href="" class="btn btn-sm btn-warning">Reset Profile Picture</a>-->
            </div>
        </div>
        <?php if($user->getCanChangeAvatar() == 1) {?>
        <form action="/user/profile_picture" method="post" class="mt-5 mb-5" enctype="multipart/form-data">
            <?php if($user->getAVatar() != 'none.png') {?>
            <div class="form-group d-flex justify-content-end mb-2">
                <input type="submit" name="remove_avatar" value="Remove Avatar" class="btn btn-danger btn-sm">
            </div>
            <?php } ?>
            <div class="form-group">
                <label for="avatar" class="form-label">Upload own Avatar (5MB max)</label>
                <input class="form-control" type="file" name="avatar" id="avatar">
            </div>
            <div class="row mt-2">
                <div class="col-12 col-lg-6 mt-2">
                    <input type="submit" name="submit" value="Upload" class="btn btn-success btn-sm">
                </div>
                <?php if(strlen($user->getMCUUID()) > 1) {?>
                <div class="col-12 col-lg-6 mt-2 text-lg-end">
                    <input type="submit" name="use_mc_avatar" value="Use Minecraft-Avatar" class="btn btn-primary btn-sm">
                </div>
                <?php } ?>
            </div>
            <?php Functions::AddCSRFCheck($csrf_token);?>
        </form>
        <?php } ?>
        <form action="/user/settings" method="POST" class="">
            <div class="form-group mb-2">
                <label for="username"><?= Functions::Translation('global.username');?></label>
                <input type="text" name="username" id="username" class="form-control" value="<?= $user->getUsername();?>" <?= $user->canChangeUsername() == false ? 'disabled' : '';?>>
            </div>
            <span><?= Functions::Translation('text.username_lastchange');?>: <?= date('d.m.Y - H:i:s', $user->getLastUsernameChange());?></span>

            <div class="form-group mt-3">
                <label for="language"><?= Functions::Translation('global.language');?></label>
                <select class="form-control" id="language" name="language">
                    <?php foreach($all_languages as $language) { ?>
                        <option value="<?= $language['language_code'];?>" <?= $language['language_code'] == $user->getLanguage() ? 'selected':'';?>><?= $language['language_name'];?></option>
                    <?php } ?>
                </select>
            </div>

            <?php if(strlen($user->getMCUUID()) > 1) {
                $mc_username = $user->getMCName();
                ?>
                    <div class="form-group mt-3">
                        <input type="submit" class="btn btn-sm btn-danger" value="Unlink Minecraft Account" name="unlink_mc"> (<?= $mc_username;?>)
                    </div>

                    <div class="form-group mt-3">
                        <label for="show_mc_name"><?= Functions::Translation('text.show_mc_name');?></label>
                        <select class="form-control" id="show_mc_name" name="show_mc_name">
                            <option value="0" <?= $user->getShowMCName() == 0 ? 'selected' : '';?>><?= Functions::Translation('no');?></option>
                            <option value="1" <?= $user->getShowMCName() == 1 ? 'selected' : '';?>><?= Functions::Translation('yes');?></option>
                        </select>
                    </div>
                <?php
            }
            else {
                ?>
                <div class="form-group mt-3 mb-2">
                    <label for="link_mc">Link Minecraft-Account</label>
                    <?php
                    if(strlen($user->getMCVerifyCode()) > 1) {
                        echo '<br>'.Functions::Translation('text.profile.mc_verify_code', ['verify_code'], [$user->getMCVerifyCode()]);
                    }
                    else {
                        ?><input type="submit" class="btn btn-sm btn-success" value="Link Minecraft Account" id="link_mc" name="link_mc"><?php
                    }?>
                </div>
                <?php
                
            } ?>

            <div class="form-group mt-3">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio"><?= $user->getBio();?></textarea>
                <script>
                    $(document).ready(function() {
                        $('#bio').summernote({
                            minHeight: 200
                        });
                    });
                </script>
            </div>

            <?php Functions::AddCSRFCheck($csrf_token); $_SESSION['user_id'] = $user->getID();?>
            <input type="hidden" name="user_id" value="<?= $user->getID();?>">
            <input type="submit" class="btn btn-success w-100 mt-3" value="<?= Functions::Translation('global.edit');?>">
        </form>
    </div>
<?php } ?>