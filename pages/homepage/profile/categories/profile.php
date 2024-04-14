<form action="/profile/settings" method="POST" class="">
    <div class="form-group mb-2">
        <label for="username"><?= Functions::Translation('global.username');?></label>
        <input type="text" name="username" id="username" class="form-control" value="<?= $user->getUsername();?>" <?= $user->canChangeUsername() == false ? 'disabled' : '';?>>
    </div>
    <span><?= Functions::Translation('text.username_lastchange');?>: <?= date('d.m.Y - H:i:s', $user->getLastUsernameChange());?></span>

    <div class="form-group mt-3">
        <label for="pronouns"><?= Functions::Translation('text.pronouns');?></label>
        <input type="text" name="pronouns" id="pronouns" class="form-control" value="<?= $user->getPronouns();?>">
    </div>

    <div class="form-group mt-3">
        <label for="language"><?= Functions::Translation('global.language');?></label>
        <select class="form-control" id="language" name="language">
            <?php foreach($all_languages as $language) { ?>
                <option value="<?= $language['language_code'];?>" <?= $language['language_code'] == $user->getLanguage() ? 'selected':'';?>><?= $language['language_name'];?></option>
            <?php } ?>
        </select>
    </div>

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

    <?php Functions::AddCSRFCheck('profile_settings', $csrf_token); $_SESSION['user_id'] = $user->getID();?>
    <input type="hidden" name="user_id" value="<?= $user->getID();?>">
    <input type="submit" class="btn btn-success w-100 mt-3" value="<?= Functions::Translation('global.edit');?>">
</form>