<?php
if(isset($GET[2]) && strtolower($GET[2]) == 'password') {
    include('password.php');
}
else {

    $all_languages = Functions::GetAllLanguages();

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
        <form action="/user/settings" method="POST" class="">
            <div class="form-group mb-2">
                <label for="username"><?= Functions::Translation('global.username');?></label>
                <input type="text" name="username" id="username" class="form-control" value="<?= $user->getUsername();?>" <?= Functions::UserCanChangeName($user->getID()) == false ? 'disabled' : '';?>>
            </div>
            <span><?= Functions::Translation('text.username_lastchange');?>: <?= Functions::UserLastNameChange($user->getID());?></span>

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
                        <label for="show_mc_name"><?= Functions::Translation('show_mc_name');?></label>
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

            <?php Functions::AddCSRFCheck(); $_SESSION['user_id'] = $user->getID();?>
            <input type="hidden" name="user_id" value="<?= $user->getID();?>">
            <input type="submit" class="btn btn-success w-100 mt-3" value="<?= Functions::Translation('global.edit');?>">
        </form>
    </div>
<?php } ?>