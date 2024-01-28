<div class="row">
    <h3 class="text-center"><?= Functions::Translation('text.settings.title');?></h3>
    <span class="text-center">Over here you can change all Website Settings</span>
</div>

<h5 class="mt-5"><?= Functions::Translation('text.settings.password_reset');?></h5>
<div class="row mt-3">
    <div class="col-12 col-lg-5">
        <div class="form-group">
            <?php $pw_reset_subject_title = Functions::Translation('text.settings.password_reset.subject');?>
            <label for="pw_reset_subject"><?= $pw_reset_subject_title;?></label>
            <input type="text" id="pw_reset_subject" class="form-control" placeholder="<?= $pw_reset_subject_title;?>" value="<?= $settings->getPasswordResetSubject();?>">
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12 col-lg-5">
        <label for="pw_reset_text"><?= Functions::Translation('text.settings.password_reset.text');?></label>
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
            <input class="form-check-input" type="checkbox" value="" id="show_copyright" <?= $settings->getCopyrightShow() == 1 ? 'checked' : '';?>>
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
            <input type="text" id="copyright_text" class="form-control" placeholder="<?= $pw_reset_subject_title;?>" value="<?= $settings->getCopyrightText();?>">
        </div>
    </div>
</div>