<?php
if(isset($GET[2]) && strtolower($GET[2]) == 'password') {
    include('change_password.php');
}
else {

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
            <div>
                <a href="/user/settings/password" class="btn btn-sm btn-success mb-2 mb-md-0"><?= Functions::Translation('change_password');?></a>
                <!--<a href="" class="btn btn-sm btn-warning">Reset Profile Picture</a>-->
            </div>
        </div>
        <form action="/user/settings" method="POST" class="">
            <div class="form-group mb-2">
                <label for="username"><?= Functions::$translations['username'];?></label>
                <input type="text" name="username" id="username" class="form-control" value="<?= $user_data['username'];?>" <?= Functions::UserCanChangeName(Functions::$user['id']) == false ? 'disabled' : '';?>>
            </div>
            <span><?= Functions::Translation('username_last_changed');?>: <?= Functions::UserLastNameChange($user_data['id']);?></span>

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
            }
            else {
                ?>
                <div class="form-group mt-3 mb-2">
                    <label for="link_mc">Link Minecraft-Account</label>
                    <?php
                    if(strlen($user_data['mc_verify_code']) > 1) {
                        echo '<br>'.Functions::Translation('use_mc_verify_code', ['verify_code'], [$user_data['mc_verify_code']]);
                    }
                    else {
                    ?><input type="submit" class="btn btn-sm btn-success" value="Link Minecraft Account" id="link_mc" name="link_mc"><?php
                    }?>
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

            <?php Functions::AddCSRFCheck(); $_SESSION['user_id'] = $user_data['id'];?>
            <input type="hidden" name="user_id" value="<?= $user_data['id'];?>">
            <input type="submit" class="btn btn-success w-100 mt-3" value="<?= Functions::$translations['edit'];?>">
        </form>
    </div>
<?php } ?>