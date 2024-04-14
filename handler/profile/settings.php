<?php

$ref = $_SERVER['HTTP_REFERER'];
$user_id = $_POST['user_id']; $s_id = $_SESSION['user_id']; unset($_SESSION['user_id']);
if(strpos($ref, Functions::GetWebsiteURL()) == 0) {
    if(Functions::CheckCSRF('profile_settings', $_POST['token'])) {
        if($user_id == $s_id) {
            if(isset($_POST['link_mc'])) {
                $key = Functions::GenerateLinkKey($user->getID());
                $user->setMCVerifyCode($key);
                $user->update();
                header("Location: /profile/settings/minecraft");
                exit;
            }

            if(isset($_POST['unlink_mc'])) {
                $log = new Log();
                $log->setCategory('Profile_Edit');
                $log->setUser($user->getID())->setTarget($user->getID());
                $log->setChangedWhat('MC-UUID');
                $log->setChangedOld($user->getMCUUID())->setChangedNew('');
                $log->setTime(gmdate('U'));
                $log->create();

                $user->setMCUUID('');
                $user->update();
                $_SESSION['success_message'] = Functions::Translation('text.success.minecraft_unlink');
                header("Location: /profile/settings/minecraft");
                exit;
            }

            $new_username = $_POST['username'];
            $new_pronouns = $_POST['pronouns'];
            $new_language = $_POST['language'];
            $new_show_mc_name = $_POST['show_mc_name'];
            $new_bio = $_POST['bio'];
            $changed = 0; $error = 0; $error_msg = '';

            if(!is_null($new_username)) {
                if(strlen($new_username) < 1 || strlen($new_username) > 64) {
                    $error = 1;
                    $error_msg = '- The input Username is invalid!';
                }
                else {
                    if($user->isUsernameRegistered($new_username) && strcmp($new_username, $user->getUsername())) {
                        $error = 1;
                        if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                        $error_msg = '- The new username ('.$new_username.') is already taken by another Account!';
                    }
                    else {
                        if(strcmp($new_username, $user->getUsername())) {
                            if($user->canChangeUsername()) {
                                $new_username = Functions::RemoveScriptFromString($new_username);
                                $new_username = Functions::RemoveIFrameFromString($new_username);
                                $new_username = htmlspecialchars($new_username);
                                
                                $log = new Log();
                                $log->setCategory('Profile_Edit');
                                $log->setUser($user->getID())->setTarget($user->getID());
                                $log->setChangedWhat('Username');
                                $log->setChangedOld($user->getUsername())->setChangedNew($new_username);
                                $log->setTime(gmdate('U'));
                                $log->create();

                                $user->setUsername($new_username);
                                $user->setLastUsernameChange(gmdate('U'));
                                $changed ++;
                            }
                            else {
                                $error = 1;
                                if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                                $error_msg = '- You can not change your username yet!';
                            }
                        }
                    }
                }
            }

            if(strlen($pronouns) > 64) {
                $error = 1;
                if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                $error_msg = '- Pronouns can not be longer than 64 characters!';
            }
            else {
                if(strcmp($new_pronouns, $user->getPronouns())) {
                    $new_pronouns = Functions::RemoveScriptFromString($new_pronouns);
                    $new_pronouns = Functions::RemoveIFrameFromString($new_pronouns);
                    $new_pronouns = htmlspecialchars($new_pronouns);

                    $log = new Log();
                    $log->setCategory('Profile_Edit');
                    $log->setUser($user->getID())->setTarget($user->getID());
                    $log->setChangedWhat('Pronouns');
                    $log->setChangedOld($user->getPronouns())->setChangedNew($new_pronouns);
                    $log->setTime(gmdate('U'));
                    $log->create();

                    $user->setPronouns($new_pronouns);
                    $changed ++;
                }
            }

            if(!Functions::LanguageExists($new_language)) {
                $error = 1;
                if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                $error_msg = '- The selected language does not exist!';
            }
            else {
                if($user->getLanguage() != $new_language) {
                   
                    $log = new Log();
                    $log->setCategory('Profile_Edit');
                    $log->setUser($user->getID())->setTarget($user->getID());
                    $log->setChangedWhat('Language');
                    $log->setChangedOld($user->getLanguage())->setChangedNew($new_language);
                    $log->setTime(gmdate('U'));
                    $log->create();

                    $user->setLanguage($new_language);
                    $changed ++;
                }
            }

            if($new_show_mc_name != $user->getShowMCName() && ($new_show_mc_name == 0 || $new_show_mc_name == 1)) {
                
                $log = new Log();
                $log->setCategory('Profile_Edit');
                $log->setUser($user->getID())->setTarget($user->getID());
                $log->setChangedWhat('Show MC-Name');
                $log->setChangedOld($user->getShowMCName())->setChangedNew($new_show_mc_name);
                $log->setTime(gmdate('U'));
                $log->create();

                $user->setShowMCName($new_show_mc_name);
                $changed ++;
            }

            if(strlen($new_bio) > 4096) {
                $error = 1;
                if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                $error_msg = '- The entered Bio is too long (4096 Character max - including HMTL)';
            }
            else {
                if(strcmp($user->getBio(), $new_bio)) {
                    $new_bio = Functions::RemoveScriptFromString($new_bio);
                    $new_bio = Functions::RemoveIFrameFromString($new_bio);
                    
                    $log = new Log();
                    $log->setCategory('Profile_Edit');
                    $log->setUser($user->getID())->setTarget($user->getID());
                    $log->setChangedWhat('Bio');
                    $log->setChangedOld($user->getBio())->setChangedNew($new_bio);
                    $log->setTime(gmdate('U'));
                    $log->create();

                    $user->setBio($new_bio);

                    $changed ++;
                }
            }

            $user->update();

            if($error == 1) {
                $_SESSION['error_title'] = 'Edit Profile';
                $_SESSION['error_message'] = $error_msg;
            }
            if($changed > 0) {
                $_SESSION['success_message'] = 'Profile edited successfully';
            }
            header("Location: /profile/settings/profile");
            exit;
        }
        else {
            $_SESSION['error_title'] = 'Edit Profile';
            $_SESSION['error_message'] = 'An error occured while editing your account. Please try again! (3)';
            header("Location: /profile/settings/profile");
            exit;
        }
    }
    else {
        $_SESSION['error_title'] = 'Edit Profile';
        $_SESSION['error_message'] = 'An error occured while editing your account. Please try again! (2)';
        header("Location: /profile/settings/profile");
        exit;
    }
}
else {
    $_SESSION['error_title'] = 'Edit Profile';
    $_SESSION['error_message'] = 'An error occured while editing your account. Please try again! (1)';
    header("Location: /profile/settings/profile");
    exit;
}