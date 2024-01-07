<?php
// AddProfileEditLog($user_id, $changed_by, $visibility, $changed_what, $changed_old, $changed_new)

$ref = $_SERVER['HTTP_REFERER'];
$id = $_POST['user_id']; $s_id = $_SESSION['user_id']; unset($_SESSION['user_id']);
if(strpos($ref, Functions::$website_url) == 0) {
    if(Functions::CheckCSRF($_POST['token'])) {
        if($id == $s_id) {
            $new_username = $_POST['username'];
            $new_language = $_POST['language'];
            $new_bio = $_POST['bio'];
            $changed = 0; $error = 0; $error_msg = '';

            $user_data = Functions::GetUserData($id);

            $user_ranks = Functions::GetUserRanks($id);
            $all_ranks = Functions::GetAllRanks();
            if(($all_ranks[$user_ranks[0]]['is_staff'] == 1 || $all_ranks[$user_ranks[1]]['is_staff']) && !Functions::UserHasPermission('admin_staff_management')) {
                $_SESSION['error_title'] = 'Permissions - Edit Staff';
                $_SESSION['error_message'] = Functions::Translation('text.error.edit_staff');
                header("Location: /admin/user/list");
                exit;
            }
            if(($all_ranks[$user_ranks[0]]['is_upperstaff'] == 1 || $all_ranks[$user_ranks[1]]['is_upperstaff']) && !Functions::UserHasPermission('admin_upperstaff_management')) {
                $_SESSION['error_title'] = 'Permissions - Edit Upper-Staff';
                $_SESSION['error_message'] = Functions::Translation('text.error.edit_upperstaff');
                header("Location: /admin/user/list");
                exit;
            }


            if(strlen($new_username) < 1 || strlen($new_username) > 64) {
                $error = 1;
                $error_msg = '- The input Username is invalid!';
            }
            else { 
                if(Functions::IsUsernameRegistered($new_username) && strcmp($new_username, $user_data['username'])) {
                    $error = 1;
                    if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                    $error_msg = '- The new username ('.$new_username.') is already taken by another Account!';
                }
                else {
                    if(strcmp($new_username, $user_data['username'])) {
                        Functions::AddProfileEditLog($user_data['id'], Functions::$user['id'], 1,'Username', $user_data['username'], $new_username);
                        $prepare = Functions::$mysqli->prepare("UPDATE web_users SET username = ? WHERE id = ?");
                        $prepare->bind_param("si", $new_username, $user_data['id']);
                        $prepare->execute();
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
                if($user_data['language'] != $new_language) {
                    Functions::AddProfileEditLog($user_data['id'], Functions::$user['id'], 1,'Language', $user_data['language'], $new_language);
                    $prepare = Functions::$mysqli->prepare("UPDATE web_users SET language = ? WHERE id = ?");
                    $prepare->bind_param("si", $new_language, $user_data['id']);
                    $prepare->execute();
                    $changed ++;
                }
            }

            $new_main_rank = $user_data['main_rank'];
            $new_secondary_rank = $user_data['secondary_rank'];
            if(Functions::UserHasPermission('admin_upperstaff_management')) {
                $all_ranks = Functions::GetAllRanks();
                $new_main_rank = $_POST['main_rank'];
                $new_secondary_rank = $_POST['secondary_rank'];
            }
            else {
                if(!Functions::RankExists($new_main_rank)) {
                    $error = 1;
                    if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                    $error_msg = '- The new username is already registered!';
                }
                else {
                    if($user_data['main_rank'] != $new_main_rank) {
                        AddProfileEditLog($user_data['id'], Functions::$user['id'], 1,'Main-Rank', $all_ranks[$user_data['main_rank']]['rank_name'], $all_ranks[$new_main_rank]['rank_name']);
                        $prepare = Functions::$mysqli->prepare("UPDATE web_users SET main_rank = ? WHERE id = ?");
                        $prepare->bind_param("ii", $new_main_rank, $user_data['id']);
                        $prepare->execute();
                        $changed ++;
                    }
                    if($user_data['secondary_rank'] != $new_secondary_rank) {
                        AddProfileEditLog($user_data['id'], Functions::$user['id'], 1,'Secondary-Rank', $all_ranks[$user_data['secondary_rank']]['rank_name'], $all_ranks[$new_secondary_rank]['rank_name']);
                        $prepare = Functions::$mysqli->prepare("UPDATE web_users SET secondary_rank = ? WHERE id = ?");
                        $prepare->bind_param("ii", $new_secondary_rank, $user_data['id']);
                        $prepare->execute();
                        $changed ++;
                    }
                }
            }

            if(strlen($new_bio) > 4096) {
                $error = 1;
                if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                $error_msg = '- The entered Bio is too long (4096 Character max - including HMTL)';
            }
            else {
                if($user_data['bio'] != $new_bio) {
                    AddProfileEditLog($user_data['id'], Functions::$user['id'], 1,'Bio', $user_data['bio'], $new_bio);
                    $prepare = Functions::$mysqli->prepare("UPDATE web_users SET bio = ? WHERE id = ?");
                    $prepare->bind_param("si", $new_bio, $user_data['id']);
                    $prepare->execute();
                    $changed ++;
                }
            }

            if($error == 1) {
                $_SESSION['error_title'] = 'Edit User';
                $_SESSION['error_message'] = $error_msg;
            }
            if($changed > 0) {
                $_SESSION['success_message'] = 'Account edited successfully';
            }
            header("Location: /admin/user/edit/".$user_data['id']);
            exit;
        }
        else {
            $_SESSION['error_title'] = 'Edit User';
            $_SESSION['error_message'] = 'An error occured while editing the User. Please try again! (3)';
            header("Location: /admin/user/edit/".$user_id);
            exit;
        }
    }
    else {
        $_SESSION['error_title'] = 'Edit User';
        $_SESSION['error_message'] = 'An error occured while editing the User. Please try again! (2)';
        header("Location: /admin/user/edit/".$user_id);
        exit;
    }
}
else {
    $_SESSION['error_title'] = 'Edit User';
    $_SESSION['error_message'] = 'An error occured while editing the User. Please try again! (1)';
    header("Location: /admin/user/edit/".$user_id);
    exit;
}