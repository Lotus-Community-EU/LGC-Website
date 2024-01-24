<div class="container w-50 mb-5">
    <div class="d-flex justify-content-between">
        <div>
            <p><?= Functions::Translation('text.change_password');?></p>
        </div>
    </div>
    <form action="/user/password" method="POST" class="">
        <div class="form-group">
            <label for="current_password"><?= Functions::Translation('text.current_password');?></label>
            <input type="password" name="current_password" class="form-control" id="current_password" placeholder="<?= Functions::Translation('text.current_password');?>">
        </div>
        <div class="form-group mt-3">
            <label for="new_password"><?= Functions::Translation('text.new_password');?></label>
            <input type="password" name="new_password" class="form-control" id="new_password" placeholder="<?= Functions::Translation('text.new_password');?>">
        </div>
        <div class="form-group mt-3">
            <label for="repeat_new_password"><?= Functions::Translation('text.repeat_password');?></label>
            <input type="password" name="repeat_new_password" class="form-control" id="repeat_new_password" placeholder="<?= Functions::Translation('text.repeat_password');?>">
        </div>

        <?php Functions::AddCSRFCheck(); $_SESSION['user_id'] = $user->getID();?>
        <input type="hidden" name="user_id" value="<?= $user->getID();?>">
        <input type="submit" class="btn btn-success w-100 mt-3" value="<?= Functions::Translation('text.change_password');?>">
    </form>
</div>
