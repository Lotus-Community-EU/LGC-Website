<div class="row justify-content-center">
    <div class="col-12 col-lg-6">
        <?php if(isset($GET[1]) && isset($GET[1]) == 'register') { ?>
            <form action="/register" method="POST" class="mb-3">
            <div class="mb-3">
                    <label for="username" class="form-label"><?= Functions::$translations['username'];?></label>
                    <input type="text" name="username" class="form-control" id="username" placeholder="<?= Functions::$translations['username'];?>">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label"><?= Functions::$translations['email'];?></label>
                    <input type="email" name="email" class="form-control" id="username" placeholder="<?= Functions::$translations['email'];?>">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label"><?= Functions::$translations['password'];?></label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="<?= Functions::$translations['password'];?>">
                </div>
                <div class="mb-3">
                    <label for="password2" class="form-label"><?= Functions::$translations['password_repeat'];?></label>
                    <input type="password2" name="password2" class="form-control" id="password2" placeholder="<?= Functions::$translations['password_repeat'];?>">
                </div>
                <?php Functions::AddCSRFCheck();?>

                <input type="submit" class="btn btn-success w-100" value="<?= Functions::$translations['login'];?>">
            </form>
            <a class="btn btn-primary float-end" href="/login"><?= Functions::$translations['login.back_to_register'];?></a>
        <?php } else {?>
            <form action="/login" method="POST" class="mb-3">
                <div class="mb-3">
                    <label for="username" class="form-label"><?= Functions::$translations['username'];?></label>
                    <input type="text" name="username" class="form-control" id="username" placeholder="<?= Functions::$translations['username'];?>">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label"><?= Functions::$translations['password'];?></label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="<?= Functions::$translations['password'];?>">
                </div>
                <div class="mb-3 float-end">
                    <div class="form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="dropdownCheck">
                        <label class="form-check-label" for="dropdownCheck">
                            <?= Functions::$translations['login.remember_me'];?>
                        </label>
                    </div>
                </div>
                <?php Functions::AddCSRFCheck();?>

                <input type="submit" class="btn btn-success w-100" value="<?= Functions::$translations['login'];?>">
            </form>
            <div class="w-100 d-flex justify-content-between align-items-center">
                <a class="align-items-center text-decoration-none text-white" href="/forgot_password"><?= Functions::$translations['login.forgot_password'];?></a>
                <a class="btn btn-primary" href="/login/register"><?= Functions::$translations['login.register'];?></a>
            </div>
        <?php } ?>
    </div>
</div>