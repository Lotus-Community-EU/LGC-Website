<?php

$ref = $_SERVER['HTTP_REFERER'];
if(strpos($ref, Functions::$website_url) == 0) {
    if(Functions::CheckCSRF($_POST['token'])) {
        if(isset($_POST['use_mc_avatar'])) {
            if(strlen($user->getMCUUID()) > 1) {
                $user->deleteProfilePictureFile();
                $avatar = file_get_contents('https://mc-heads.net/avatar/'.$user->getMCUUID().'/nohelm');
                $avatar_name = $user->getID().'.'.time();
                file_put_contents('assets/images/avatar/'.$avatar_name.'.png', $avatar);
                $user->setProfilePicture($avatar_name.'.png');
                $user->update();
                $_SESSION['success_message'] = 'You are now using your Minecraft-Avatar as Avatar!';
                header("Location: /user/settings");
                exit;
            }
            else {
                $_SESSION['error_title'] = 'Edit Profile Picture';
                $_SESSION['error_message'] = 'You didn\'t link your Minecraft-Account!';
                header("Location: /user/settings");
                exit;
            }
        }
        if(isset($_POST['remove_avatar'])) {
            $user->deleteProfilePictureFile();
            $user->setProfilePicture('none.png');
            $user->update();
            $_SESSION['success_message'] = 'You removed your Avatar!';
            header("Location: /user/settings");
            exit;
        }
    }
    else {
        $_SESSION['error_title'] = 'Edit Profile Picture';
        $_SESSION['error_message'] = 'An error occured while editing your profile picture. Please try again! (2)';
        header("Location: /user/settings");
        exit;
    }
}
else {
    $_SESSION['error_title'] = 'Edit Profile Picture';
    $_SESSION['error_message'] = 'An error occured while editing your profile picture. Please try again! (1)';
    header("Location: /user/settings");
    exit;
}