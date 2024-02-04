<?php
// AddProfileEditLog($user_id, $changed_by, $visibility, $changed_what, $changed_old, $changed_new)

$ref = $_SERVER['HTTP_REFERER'];
$id = $_POST['user_id']; $s_id = $_SESSION['user_id']; unset($_SESSION['user_id']);
if(strpos($ref, Functions::$website_url) == 0) {
    if(Functions::CheckCSRF('admin_edit_user', $_POST['token'])) {
        if($id == $s_id) {
            $new_username = $_POST['username'];
            $new_language = $_POST['language'];
            $new_bio = $_POST['bio'];
            $new_can_change_avatar = is_numeric($_POST['can_change_avatar']) ? $_POST['can_change_avatar'] : 0;
            $changed = 0; $error = 0; $error_msg = '';

            $user_data = new User($id);
            $main_rank = new Rank($user_data->getMainRank());
            $secondary_rank = new Rank($user_data->getSecondaryRank());

            if(($main_rank->getIsStaff() == 1 || $secondary_rank->getIsStaff() == 1) && !$user->hasPermission('admin_staff_management')) {
                $_SESSION['error_title'] = 'Permissions - Edit Staff';
                $_SESSION['error_message'] = Functions::Translation('text.error.edit_staff');
                header("Location: /admin/user/list");
                exit;
            }
            if(($main_rank->getIsUpperStaff() == 1 || $secondary_rank->getIsUpperStaff() == 1) && !$user->hasPermission('admin_upperstaff_management')) {
                $_SESSION['error_title'] = 'Permissions - Edit Upper-Staff';
                $_SESSION['error_message'] = Functions::Translation('text.error.edit_upperstaff');
                header("Location: /admin/user/list");
                exit;
            }


            if(strlen($new_username) < $user_data->lengths['username']['min'] || strlen($new_username) > $user_data->lengths['username']['max']) {
                $error = 1;
                $error_msg = '- The input Username has to be between '.$user_data->lengths['username']['min'].' and '.$user_data->lengths['username']['max'].' characters!';
            }
            else { 
                if($registered_id = $user_data->isUsernameRegistered($new_username) && strcmp($new_username, $user_data->getUsername())) {
                    $error = 1;
                    if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                    $error_msg = '- The new username (<a href="/user/'.$registered_id.'" target="_blank">'.$new_username.'</a>) is already taken by another Account!';
                }
                else {
                    if(strcmp($new_username, $user_data->getUsername())) {
                        $new_username = Functions::RemoveScriptFromString($new_username);
                        $new_username = Functions::RemoveIFrameFromString($new_username);
                        $new_username = htmlspecialchars($new_username);
                        $log = new Log();
                        $log->setCategory('Profile_Edit');
                        $log->setUser($user->getID())->setTarget($user_data->getID());
                        $log->setChangedWhat('Username')->setChangedOld($user_data->getUsername())->setChangedNew($new_username);
                        $log->setTime(gmdate('U'));
                        $log->create();
                        
                        $user_data->setUsername($new_username);
                        
                        $changed ++;
                    }
                }
            }

            if(!Functions::LanguageExists($new_language)) {
                $error = 1;
                if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                $error_msg = '- The selected language does not exist!';
            }
            else {
                if($user_data->getLanguage() != $new_language) {
                    $log = new Log();
                    $log->setCategory('Profile_Edit');
                    $log->setUser($user->getID())->setTarget($user_data->getID());
                    $log->setChangedWhat('Language')->setChangedOld($user_data->getLanguage())->setChangedNew($new_language);
                    $log->setTime(gmdate('U'));
                    $log->create();

                    $user_data->setLanguage($new_language);
                    
                    $changed ++;
                }
            }

            if($user_data->getCanChangeAvatar() != $new_can_change_avatar) {
                $log = new Log();
                $log->setCategory('Profile_Edit');
                $log->setUser($user->getID())->setTarget($user_data->getID());
                $log->setChangedWhat('Change Avatar')->setChangedOld($user_data->getCanChangeAvatar())->setChangedNew($new_can_change_avatar);
                $log->setTime(gmdate('U'));
                $log->create();

                $user_data->setCanChangeAvatar($new_can_change_avatar);
                
                $changed ++;
            }

            if(isset($_POST['remove_avatar'])) {
                $log = new Log();
                $log->setCategory('Profile_Edit');
                $log->setUser($user->getID())->setTarget($user_data->getID());
                $log->setChangedWhat('Profile Picture');
                $log->setChangedOld($user->getAvatar())->setChangedNew('none.png');
                $log->setTime(gmdate('U'));
                $log->create();

                $user_data->deleteAvatarFile();
                $user_data->setAvatar('none.png');

                $changed ++;
            }

            $new_main_rank = $user_data->getMainRank();
            $new_secondary_rank = $user_data->getSecondaryRank();
            if($user->hasPermission('admin_upperstaff_management')) {
                $new_main_rank = new Rank($_POST['main_rank']);
                $new_secondary_rank = new Rank($_POST['secondary_rank']);

                if($new_main_rank == null || $new_secondary_rank == null) {
                    $error = 1;
                    if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                    $error_msg = '- The new new Main-Rank or Secondary-Rank do not exist!';
                }
                else {
                    if($user_data->getMainRank() != $new_main_rank->getID()) {
                        
                        $log = new Log();
                        $log->setCategory('Profile_Edit');
                        $log->setUser($user->getID())->setTarget($user_data->getID());
                        $log->setChangedWhat('Main-Rank')->setChangedOld($main_rank->getName().' ('.$main_rank->getID().')')->setChangedNew($new_main_rank->getName().' ('.$new_main_rank->getID().')');
                        $log->setTime(gmdate('U'));
                        $log->create();

                        $user_data->setMainRank($new_main_rank->getID());
                        
                        $changed ++;
                    }
                    if($user_data->getSecondaryRank() != $new_secondary_rank->getID()) {

                        $log = new Log();
                        $log->setCategory('Profile_Edit');
                        $log->setUser($user->getID())->setTarget($user_data->getID());
                        $log->setChangedWhat('Secondary-Rank')->setChangedOld($secondary_rank->getName().' ('.$secondary_rank->getID().')')->setChangedNew($new_secondary_rank->getName().' ('.$new_secondary_rank->getID().')');
                        $log->setTime(gmdate('U'));
                        $log->create();

                        $user_data->setSecondaryRank($new_secondary_rank->getID());
                        
                        $changed ++;
                    }
                }
            }

            if(strlen($new_bio) > $user_data->lengths['bio']['max']) {
                $error = 1;
                if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                $error_msg = '- The entered Bio is too long ('.$user_data->lengths['bio']['max'].' Character max - including HMTL/Formatting)';
            }
            else {
                if(strcmp($user_data->getBio(), $new_bio)) {
                    $new_bio = Functions::RemoveScriptFromString($new_bio);
                    $new_bio = Functions::RemoveIFrameFromString($new_bio);
                    
                    $log = new Log();
                    $log->setCategory('Profile_Edit');
                    $log->setUser($user->getID())->setTarget($user_data->getID());
                    $log->setChangedWhat('Bio')->setChangedOld($user_data->getBio())->setChangedNew($new_bio);
                    $log->setTime(gmdate('U'));
                    $log->create();

                    $user_data->setBio($new_bio);

                    $changed ++;
                }
            }

            $user_data->update();

            if($error == 1) {
                $_SESSION['error_title'] = 'Edit User';
                $_SESSION['error_message'] = $error_msg;
            }
            if($changed > 0) {
                $_SESSION['success_message'] = 'Account edited successfully';
            }
            header("Location: /admin/user/edit/".$user_data->getID());
            exit;
        }
        else {
            $_SESSION['error_title'] = 'Edit User';
            $_SESSION['error_message'] = 'An error occured while editing the User. Please try again! (3)';
            header("Location: /admin/user/edit/".$id);
            exit;
        }
    }
    else {
        $_SESSION['error_title'] = 'Edit User';
        $_SESSION['error_message'] = 'An error occured while editing the User. Please try again! (2)';
        header("Location: /admin/user/edit/".$id);
        exit;
    }
}
else {
    $_SESSION['error_title'] = 'Edit User';
    $_SESSION['error_message'] = 'An error occured while editing the User. Please try again! (1)';
    header("Location: /admin/user/edit/".$id);
    exit;
}