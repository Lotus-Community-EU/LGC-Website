<form action="/register" method="POST" class="mb-3">
    <div class="mb-3">
        <label for="username" class="form-label"><?= Functions::Translation('global.username');?></label>
        <input type="text" name="username" class="form-control" id="username" placeholder="<?= Functions::Translation('global.username');?>">
    </div>
    <div class="mb-3">
        <label for="email" class="form-label"><?= Functions::Translation('text.email');?></label>
        <input type="email" name="email" class="form-control" id="email" placeholder="<?= Functions::Translation('text.email');?>">
    </div>
    <div class="mb-3">
        <label for="password" class="form-label"><?= Functions::Translation('global.password');?></label>
        <input type="password" name="password" class="form-control" id="password" placeholder="<?= Functions::Translation('global.password');?>">
    </div>
    <div class="mb-3">
        <label for="password2" class="form-label"><?= Functions::Translation('text.repeat_password');?></label>
        <input type="password" name="password2" class="form-control" id="password2" placeholder="<?= Functions::Translation('text.repeat_password');?>">
    </div>
    <?php Functions::AddCSRFCheck('Register');?>

    <input type="submit" class="btn btn-success w-100" value="<?= Functions::Translation('text.register');?>">
</form>