<?php
if(strlen($user->getMCUUID()) > 1) {
    $mc_username = $user->getMCName();
    ?>
        <div class="form-group">
            <input type="submit" class="btn btn-sm btn-danger" value="Unlink Minecraft Account" name="unlink_mc"> (<?= $mc_username;?>)
        </div>

        <div class="form-group mt-3">
            <label for="show_mc_name"><?= Functions::Translation('text.show_mc_name');?></label>
            <select class="form-control" id="show_mc_name" name="show_mc_name">
                <option value="0" <?= $user->getShowMCName() == 0 ? 'selected' : '';?>><?= Functions::Translation('no');?></option>
                <option value="1" <?= $user->getShowMCName() == 1 ? 'selected' : '';?>><?= Functions::Translation('yes');?></option>
            </select>
        </div>

        <br><hr><br>

        <?php
            $bridge = $user->getMCChatBridge();
            $bridge = explode(';', $bridge);
            if($bridge == null) {
                echo 'Contact Web Development with a Screenshot of the whole Website please!';
            }
            else {
                foreach($bridge as $p => $point) {
                    echo '['.$p.'] - '.$point.'<br>';
                }
                //echo var_dump($user->getMCChatBridge());
            }
        ?>

        <?php Functions::AddCSRFCheck('profile_settings', $csrf_token); $_SESSION['user_id'] = $user->getID();?>
        <input type="hidden" name="user_id" value="<?= $user->getID();?>">
        <input type="submit" class="btn btn-success w-100 mt-3" value="<?= Functions::Translation('global.edit');?>">
    
    <?php
}
else {
    ?>
    <div class="form-group">
        <label for="link_mc">Link Minecraft-Account</label>
        <?php
        if(strlen($user->getMCVerifyCode()) > 1) {
            echo '<br>'.Functions::Translation('text.profile.mc_verify_code', ['verify_code'], [$user->getMCVerifyCode()]);
        }
        else {
            ?>
            <input type="submit" class="btn btn-sm btn-success" value="Link Minecraft Account" id="link_mc" name="link_mc">
            <?php
            Functions::AddCSRFCheck('profile_settings', $csrf_token);
        }?>
    </div>
    <?php
}
?>