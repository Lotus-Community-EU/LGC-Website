<div class="row justify-content-center">
    <div class="col-12 col-lg-6">
        <?php if(isset($GET[1]) && isset($GET[1]) == 'register') { ?>
            <form action="/register" method="POST" class="mb-3">
            <div class="mb-3">
                    <label for="username" class="form-label"><?= Functions::Translation('global.username');?></label>
                    <input type="text" name="username" class="form-control" id="username" placeholder="<?= Functions::Translation('global.username');?>">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label"><?= Functions::Translation('text.email');?></label>
                    <input type="email" name="email" class="form-control" id="username" placeholder="<?= Functions::Translation('text.email');?>">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label"><?= Functions::Translation('global.password');?></label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="<?= Functions::Translation('global.password');?>">
                </div>
                <div class="mb-3">
                    <label for="password2" class="form-label"><?= Functions::Translation('text.repeat_password');?></label>
                    <input type="password2" name="password2" class="form-control" id="password2" placeholder="<?= Functions::Translation('text.repeat_password');?>">
                </div>
                <?php Functions::AddCSRFCheck();?>

                <input type="submit" class="btn btn-success w-100" value="<?= Functions::Translation('text.login');?>">
            </form>
            <a class="btn btn-primary float-end" href="/login"><?= Functions::Translation('login.back_to_register');?></a>
        <?php } else {?>
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
                <?php Functions::AddCSRFCheck();?>

                <input type="submit" class="btn btn-success w-100" value="<?= Functions::Translation('text.login');?>">
            </form>
            <div class="w-100 d-flex justify-content-between align-items-center">
                <a class="align-items-center text-decoration-none text-white" href="/forgot_password"><?= Functions::Translation('login.forgot_password');?></a>
                <a class="btn btn-primary" href="/login/register"><?= Functions::Translation('login.register');?></a>
            </div>
        <?php } ?>
    </div>
</div>