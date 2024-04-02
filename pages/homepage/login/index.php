<div class="row justify-content-center">
    <div class="col-12 col-lg-6">
        <?php if(isset($GET[1]) && isset($GET[1]) == 'register') { ?>
            <?php include('form_register.php');?>
            <a class="btn btn-primary float-end" href="/login"><?= Functions::Translation('login.back_to_register');?></a>
        <?php } else {?>
            <?php include('form_login.php');?>
            <div class="w-100 d-flex justify-content-between align-items-center">
                <a class="align-items-center text-decoration-none text-white" href="/forgot_password"><?= Functions::Translation('login.forgot_password');?></a>
                <a class="btn btn-primary" href="/login/register"><?= Functions::Translation('login.register');?></a>
            </div>
        <?php } ?>
    </div>
</div>