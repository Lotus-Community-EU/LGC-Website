<form action="/login" method="POST" class="mb-3">
    <div class="mb-3">
        <label for="username" class="form-label"><?= Functions::Translation('global.username');?></label>
        <input type="text" name="username" class="form-control" id="username" placeholder="<?= Functions::Translation('global.username');?>">
    </div>
    <div class="mb-3">
        <label for="password" class="form-label"><?= Functions::Translation('global.password');?></label>
        <input type="password" name="password" class="form-control" id="password" placeholder="<?= Functions::Translation('global.password');?>">
    </div>
    <div class="mb-3 float-end">
        <div class="form-check">
            <input type="checkbox" name="remember" class="form-check-input" id="dropdownCheck">
            <label class="form-check-label" for="dropdownCheck">
                <?= Functions::Translation('login.remember_me');?>
            </label>
        </div>
    </div>
    <?php Functions::AddCSRFCheck('Login');?>

    <input type="submit" class="btn btn-success w-100" value="<?= Functions::Translation('text.login');?>">
</form>