<?php
$ref = $_SERVER['HTTP_REFERER'];
if(strpos($ref, Functions::GetWebsiteURL()) == 0) {
    if(Functions::CheckCSRF('website_settings', $_POST['token'])) {

        $new_pw_reset_subject = $_POST['pw_reset_subject'];
        $new_pw_reset_text = $_POST['pw_reset_text'];

        $new_show_copyright = isset($_POST['show_copyright']) ? 1 : 0;
        $new_copyright_text = $_POST['copyright_text'];

        $new_show_mc_heads = isset($_POST['show_mc_heads']) ? 1 : 0;
        
        $new_username_change_value = $_POST['username_change_value'];
        $new_username_change_unit = $_POST['username_change_unit'];

        $new_max_avatar_size = (int) $_POST['max_avatar_size'];

        $success_msg = '';
        $error_msg = '';

        if(strlen($new_pw_reset_subject) >= 1 && strlen($new_pw_reset_subject) <= 32) {
            if(strcmp($new_pw_reset_subject, $settings->getPasswordResetSubject())) {

                $log = new Log();
                $log->setCategory('Website_Settings');
                $log->setUser($user->getID())->setTarget('Website Settings');
                $log->setChangedWhat('PW-Reset Subject');
                $log->setChangedOld($settings->getPasswordResetSubject())->setChangedNew($new_pw_reset_subject);
                $log->setTime(gmdate('U'));
                $log->create();

                $settings->setPasswordResetSubject($new_pw_reset_subject);
                $success_msg = '- Password-Reset E-Mail Subject edited successfully!';
            }
        }
        else {
            $error_msg = 'Password-Reset E-Mail Subject has to be between 1 and 32 characters!';
        }

        if(strlen($new_pw_reset_text) >= 1 && strlen($new_pw_reset_text) < 50000) {
            if(strpos($new_pw_reset_text,'%new_password%') !== false) {
                if(strcmp($new_pw_reset_text, $settings->getPasswordResetText())) {

                    $log = new Log();
                    $log->setCategory('Website_Settings');
                    $log->setUser($user->getID())->setTarget('Website Settings');
                    $log->setChangedWhat('PW-Reset Text');
                    $log->setChangedOld($settings->getPasswordResetText())->setChangedNew($new_pw_reset_text);
                    $log->setTime(gmdate('U'));
                    $log->create();

                    $settings->setPasswordResetText($new_pw_reset_text);
                    if(strlen($success_msg) > 0) {$success_msg .= '<br>';}
                    $success_msg .= '- Password-Reset E-Mail Text edited successfully!';
                }
            }
            else {
                if(strlen($error_msg) > 0) {$error_msg .= '<br>';}
                $error_msg .= '- The variable %new_password% has to be present in the E-Mail!';
            }
        }
        else {
            if(strlen($error_msg) > 0) {$error_msg .= '<br>';}
            $error_msg .= '- Password-Reset E-Mail Text has to be between 1 and 50.000 characters!';
        }

        if($new_show_copyright != $settings->getCopyrightShow()) {

            $log = new Log();
            $log->setCategory('Website_Settings');
            $log->setUser($user->getID())->setTarget('Website Settings');
            $log->setChangedWhat('Copyright Show');
            $log->setChangedOld($settings->getCopyrightShow())->setChangedNew($new_show_copyright);
            $log->setTime(gmdate('U'));
            $log->create();

            $settings->setCopyrightShow($new_show_copyright);
            if(strlen($success_msg) > 0) {$success_msg .= '<br>';}
            $success_msg .= '- Copyright-Show has been toggled!';
        }

        if(strlen($new_copyright_text) < 32) {
            if(strlen($new_copyright_text) == 0) {

                $log = new Log();
                $log->setCategory('Website_Settings');
                $log->setUser($user->getID())->setTarget('Website Settings');
                $log->setChangedWhat('Copyright Text');
                $log->setChangedOld($settings->getCopyrightText())->setChangedNew('');
                $log->setTime(gmdate('U'));
                $log->create();

                $settings->setCopyrightText('');
                $settings->setCopyrightShow(0);
                if(strlen($error_msg) > 0) {$error_msg .= '<br>';}
                $error_msg .= '- Copyright-Show has been deactivated due to the message being empty!';
            }
            else {
                if(strcmp($new_copyright_text, $settings->getCopyrightText())) {
                    $new_copyright_text = Functions::RemoveScriptFromString($new_copyright_text);
                    $new_copyright_text = Functions::RemoveIFrameFromString($new_copyright_text);

                    $log = new Log();
                    $log->setCategory('Website_Settings');
                    $log->setUser($user->getID())->setTarget('Website Settings');
                    $log->setChangedWhat('Copyright Text');
                    $log->setChangedOld($settings->getCopyrightText())->setChangedNew($new_copyright_text);
                    $log->setTime(gmdate('U'));
                    $log->create();

                    $settings->setCopyrightText($new_copyright_text);
                    if(strlen($success_msg) > 0) {$success_msg .= '<br>';}
                    $success_msg .= '- Copyright-Text has been updated successfully!';
                }
            }
        }

        if($new_show_mc_heads != $settings->getShowMCHeads()) {

            $log = new Log();
            $log->setCategory('Website_Settings');
            $log->setUser($user->getID())->setTarget('Website Settings');
            $log->setChangedWhat('Show MC-Heads Credits');
            $log->setChangedOld($settings->getShowMCHeads)->setChangedNew($new_show_mc_heads);
            $log->setTime(gmdate('U'));
            $log->create();

            $settings->setShowMCHeads($new_show_mc_heads);
            if(strlen($success_msg) > 0) {$success_msg .= '<br>';}
            $success_msg .= '- MC-Heads credits in Footer has been toggled!';
        }

        /*
            $new_username_change_value = $_POST['username_change_value'];
            $new_username_change_unit = $_POST['username_change_value'];
        */

        if(is_numeric($new_username_change_value)) {
            if($new_username_change_value > 0 && $new_username_change_value <= 12) {
                if($new_username_change_value != $settings->getUsernameChangeValue()) {

                    $log = new Log();
                    $log->setCategory('Website_Settings');
                    $log->setUser($user->getID())->setTarget('Website Settings');
                    $log->setChangedWhat('Username-Change Value');
                    $log->setChangedOld($settings->getUsernameChangeValue)->setChangedNew($new_username_change_value);
                    $log->setTime(gmdate('U'));
                    $log->create();

                    $settings->setUsernameChangeValue($new_username_change_value);
                    if(strlen($success_msg) > 0) {$success_msg .= '<br>';}
                    $success_msg .= '- Username-Change value has been updated successfully!';
                }
            }
            else {
                if(strlen($error_msg) > 0) {$error_msg .= '<br>';}
                $error_msg .= '- Username-Change value has to be between 1 and 12!';
            }
        }
        else {
            if(strlen($error_msg) > 0) {$error_msg .= '<br>';}
            $error_msg .= '- Username-Change value has to be numeric!';
        }

        if(!strcmp($new_username_change_unit,'hours') || !strcmp($new_username_change_unit,'days') || !strcmp($new_username_change_unit,'months')) {
            if(strcmp($new_username_change_unit, $settings->getUsernameChangeUnit())) {

                $log = new Log();
                $log->setCategory('Website_Settings');
                $log->setUser($user->getID())->setTarget('Website Settings');
                $log->setChangedWhat('Username-Change Unit');
                $log->setChangedOld($settings->getUsernameChangeUnit())->setChangedNew($new_username_change_unit);
                $log->setTime(gmdate('U'));
                $log->create();

                $settings->setUsernameChangeUnit($new_username_change_unit);
                if(strlen($success_msg) > 0) {$success_msg .= '<br>';}
                $success_msg .= '- Username-Change unit has been updated successfully!';
            }
        }
        else {
            if(strlen($error_msg) > 0) {$error_msg .= '<br>';}
            $error_msg .= '- Username-Change unit is invalid (only \'hours\', \'days\' and \'months\' are accepted)!';
        }

        if($new_max_avatar_size != $settings->getMaxAvatarSize()) {
            if(is_integer($new_max_avatar_size)) {
                if($new_max_avatar_size > 0) {

                    $log = new Log();
                    $log->setCategory('Website_Settings');
                    $log->setUser($user->getID())->setTarget('Website Settings');
                    $log->setChangedWhat('Max. Avatar Size (MB)');
                    $log->setChangedOld($settings->getMaxAvatarSize())->setChangedNew($new_max_avatar_size);
                    $log->setTime(gmdate('U'));
                    $log->create();

                    $settings->setMaxAvatarSize($new_max_avatar_size);
                    if(strlen($success_msg) > 0) {$success_msg .= '<br>';}
                    $success_msg .= '- Max. Avatar-Size has been changed!';
                }
                else {
                    if(strlen($error_msg) > 0) {$error_msg .= '<br>';}
                    $error_msg .= '- Max. Avatar Size has to be at least 1 MB!';
                }
            }
            else {
                if(strlen($error_msg) > 0) {$error_msg .= '<br>';}
                $error_msg .= '- Don\'t use a comma for the Max. Avatar Size!';
            }
        }

        if(strlen($success_msg) > 0) {
            $_SESSION['success_message'] = $success_msg;
        }
        if(strlen($error_msg) > 0) {
            $_SESSION['error_title'] = 'Website Settings';
            $_SESSION['error_message'] = $error_msg;
        }

        header("Location: /admin/website_settings");
        exit;

    }
    else {
        $_SESSION['error_title'] = 'Website Settings';
        $_SESSION['error_message'] = 'An error occured while changing the Website Settings. Please try again! (2)';
        header("Location: /admin/website_settings");
        exit;
    }
}
else {
    $_SESSION['error_title'] = 'Login';
    $_SESSION['error_message'] = 'An error occured while changing the Website Settings. Please try again! (1)';
    header("Location: /admin/website_settings");
    exit;
}