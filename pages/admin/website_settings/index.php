<div class="row">
    <h3 class="text-center"><?= Functions::Translation('text.settings.title');?></h3>
    <span class="text-center">Over here you can change all Website Settings</span>
</div>

<form action="/admin/website_settings" method="POST">
    <h5 class="mt-5"><?= Functions::Translation('text.settings.password_reset');?></h5>
    <div class="row mt-3">
        <div class="col-12 col-lg-5">
            <div class="form-group">
                <?php $pw_reset_subject_title = Functions::Translation('text.settings.password_reset.subject');?>
                <label for="pw_reset_subject"><?= $pw_reset_subject_title;?></label>
                <input type="text" id="pw_reset_subject" name="pw_reset_subject" class="form-control" placeholder="<?= $pw_reset_subject_title;?>" value="<?= $settings->getPasswordResetSubject();?>">
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12 col-lg-5">
            <label for="pw_reset_text"><?= Functions::Translation('text.settings.password_reset.text');?></label>
            <p>
                Variables:<br>
                <ul>
                    <li>%staff_member% = Staff's name who reseted the Password</li>
                    <li>%reset_time% = When the password reset was requested (UTC)</li>
                    <li>%new_password% = The new password the user gets send.</li>
                </ul>
            </p>
            <textarea id="pw_reset_text" name="pw_reset_text"><?= $settings->getPasswordResetText();?></textarea>
            <script>
                $(document).ready(function() {
                    $('#pw_reset_text').summernote({
                        minHeight: 200
                    });
                });
            </script>
        </div>
    </div>

    <h5 class="mt-5"><?= Functions::Translation('text.settings.copyright');?></h5>
    <div class="row mt-3">
        <div class="col-12 col-lg-5">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" name="show_copyright" id="show_copyright" <?= $settings->getCopyrightShow() == 1 ? 'checked' : '';?>>
                <label class="form-check-label" for="show_copyright">
                    <?= Functions::Translation('text.settings.copyright.show');?>
                </label>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12 col-lg-5">
            <div class="form-group">
                <label for="copyright_text">Copyright Text</label>
                <input type="text" id="copyright_text" class="form-control" name="copyright_text" placeholder="Copyright Text" value="<?= $settings->getCopyrightText();?>">
            </div>
        </div>
    </div>

    <h5 class="mt-5"><?= Functions::Translation('text.settings.mc_heads');?></h5>
    <div class="row mt-3">
        <div class="col-12 col-lg-5">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" name="show_mc_heads" id="show_mc_heads" <?= $settings->getShowMCHeads() == 1 ? 'checked' : '';?>>
                <label class="form-check-label" for="show_mc_heads">
                    <?= Functions::Translation('text.settings.mc_heads.label');?>
                </label>
            </div>
        </div>
    </div>

    <h5 class="mt-5"><?= Functions::Translation('text.settings.username_change');?></h5>
    <div class="row mt-3 row-cold-auto">

        <div class="col-3 col-md-2">
            <div class="form-group">
                <input type="text" class="form-control" value="<?= $settings->getUsernameChangeValue();?>" name="username_change_value" placeholder="<?= Functions::Translation('text.value');?>">
            </div>
        </div>

        <div class="col-6 col-md-3">
            <?php $unit = $settings->getUsernameChangeUnit();?>
            <select class="form-select" name="username_change_unit">
                <option value="0"><?= Functions::Translation('text.choose');?></option>
                <option value="hours" <?= $unit == 'hours' ? 'selected' : '';?>>Hours</option>
                <option value="days" <?= $unit == 'days' ? 'selected' : '';?>>Days</option>
                <option value="months" <?= $unit == 'months' ? 'selected' : '';?>>Months</option>
            </select>
        </div>
    </div>

    <h5 class="mt-5"><?= Functions::Translation('text.settings.max_avatar_size');?></h5>
    <div class="row mt-3">
        <div class="col-12 col-lg-5">
            <div class="form-group">
                <label for="max_avatar_size"><?= Functions::Translation('text.settings.max_avatar_size');?></label>
                <div class="input-group w-25">
                    <input type="text" class="form-control" name="max_avatar_size" id="max_avatar_size" value="<?= $settings->getMaxAvatarSize();?>">
                    <span class="input-group-text">MB</span>
                </div>
            </div>
        </div>
    </div>

    <?php Functions::AddCSRFCheck('website_settings');?>
    <input type="submit" value="<?= Functions::Translation('global.edit');?>" class="btn btn-success w-100 mt-5">
</form>