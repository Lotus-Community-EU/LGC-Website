<?php

$ref = $_SERVER['HTTP_REFERER'];
if(strpos($ref, Functions::GetWebsiteURL()) == 0) {
    if(Functions::CheckCSRF('profile_settings', $_POST['token'])) {
        if($user->getCanChangeAVatar() == 1) {
            if(isset($_POST['use_mc_avatar'])) {
                if(strlen($user->getMCUUID()) > 1) {
                    $user->deleteAvatarFile();
                    $avatar = file_get_contents('https://mc-heads.net/avatar/'.$user->getMCUUID().'/nohelm');
                    $avatar_name = 'mc.'.$user->getID().'.'.time().'.png';

                    $log = new Log();
                    $log->setCategory('Profile_Edit');
                    $log->setUser($user->getID())->setTarget($user->getID());
                    $log->setChangedWhat('Profile Picture');
                    $log->setChangedOld($user->getAvatar())->setChangedNew($avatar_name);
                    $log->setTime(gmdate('U'));
                    $log->create();

                    file_put_contents('assets/images/avatar/'.$avatar_name, $avatar);
                    $user->setAvatar($avatar_name);
                    $user->update();
                    $_SESSION['success_message'] = 'You are now using your Minecraft-Avatar as Avatar!';
                    header("Location: /profile/settings");
                    exit;
                }
                else {
                    $_SESSION['error_title'] = 'Edit Profile Picture';
                    $_SESSION['error_message'] = 'You didn\'t link your Minecraft-Account!';
                    header("Location: /profile/settings");
                    exit;
                }
            }
            if(isset($_POST['remove_avatar'])) {
                $user->deleteAvatarFile();

                $log = new Log();
                $log->setCategory('Profile_Edit');
                $log->setUser($user->getID())->setTarget($user->getID());
                $log->setChangedWhat('Profile Picture');
                $log->setChangedOld($user->getAvatar())->setChangedNew('none.png');
                $log->setTime(gmdate('U'));
                $log->create();

                $user->setAvatar('none.png');
                $user->update();
                $_SESSION['success_message'] = 'You removed your Avatar!';
                header("Location: /profile/settings");
                exit;
            }
            if(isset($_POST['submit'])) {
                $avatar = $_FILES['avatar'];
                if(strlen($avatar['name']) > 0) {
                    if($avatar['size'] <= ($settings->getMaxAvatarSize()*1024)*1000000) { // 1024KB = 1 MB | 1.000.000 Byte = 1MB
                        if($avatar['type'] == 'image/jpeg' || $avatar['type'] == 'image/png' || $avatar['type'] == 'image/gif') {
                            $user->deleteAvatarFile();
                            $name = $avatar['name'];
                            $name = explode('.', $name);
                            $ending = end($name);
                            $avatar_name = $user->getID().'.'.time().'.'.$ending;

                            $log = new Log();
                            $log->setCategory('Profile_Edit');
                            $log->setUser($user->getID())->setTarget($user->getID());
                            $log->setChangedWhat('Profile Picture');
                            $log->setChangedOld($user->getAvatar())->setChangedNew($avatar_name);
                            $log->setTime(gmdate('U'));
                            $log->create();

                            move_uploaded_file($avatar['tmp_name'],'assets/images/avatar/'.$avatar_name);
                            $user->setAvatar($avatar_name);
                            $user->update();
                            $_SESSION['success_message'] = 'You successfully uploaded an Avatar!';
                            header("Location: /profile/settings");
                            exit;
                        }
                        else {
                            $_SESSION['error_title'] = 'Edit Avatar';
                            $_SESSION['error_message'] = 'Avatars can only be .png, .jpeg or .gif files!';
                            header("Location: /profile/settings");
                            exit;
                        }
                    }
                    else {
                        $_SESSION['error_title'] = 'Edit Avatar';
                        $_SESSION['error_message'] = 'Avatar can not be bigger than '.$settings->getMaxAvatarSize().' MB!';
                        header("Location: /profile/settings");
                        exit;
                    }
                }
                else {
                    $_SESSION['error_title'] = 'Edit Avatar';
                    $_SESSION['error_message'] = 'Please select an Avatar you want to upload!';
                    header("Location: /profile/settings");
                    exit;
                }
            }
        }
        else {
            $_SESSION['error_title'] = 'Edit Avatar';
            $_SESSION['error_message'] = 'Your permissions to change your Avatar have been rejected. Contact Staff, if you think that this is an error!';
            header("Location: /profile/settings");
            exit;
        }
    }
    else {
        $_SESSION['error_title'] = 'Edit Avatar';
        $_SESSION['error_message'] = 'An error occured while editing your Avatar. Please try again! (2)';
        header("Location: /profile/settings");
        exit;
    }
}
else {
    $_SESSION['error_title'] = 'Edit Avatar';
    $_SESSION['error_message'] = 'An error occured while editing your Avatar. Please try again! (1)';
    header("Location: /profile/settings");
    exit;
}