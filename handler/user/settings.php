<?php

// AddProfileEditLog($user_id, $changed_by, $visibility, $changed_what, $changed_old, $changed_new)

$ref = $_SERVER['HTTP_REFERER'];
$user_id = $_POST['user_id']; $s_id = $_SESSION['user_id']; unset($_SESSION['user_id']);
if(strpos($ref, Functions::$website_url) == 0) {
    if(Functions::CheckCSRF($_POST['token'])) {
        if($user_id == $s_id) {
            if(isset($_POST['link_mc'])) {
                $key = Functions::GenerateLinkKey(Functions::$user['id']);
                $prepare = Functions::$mysqli->prepare("UPDATE web_users SET mc_verify_code = ? WHERE id = ?");
                $prepare->bind_param("si", $key, Functions::$user['id']);
                $prepare->execute();
                header("Location: /user/settings");
                exit;
            }

            if(isset($_POST['unlink_mc'])) {
                Functions::AddProfileEditLog(Functions::$user['id'], Functions::$user['id'], 1,'MC-UUID', Functions::$user['mc_uuid'],'');
                $prepare = Functions::$mysqli->prepare("UPDATE web_users SET mc_uuid = '' WHERE id = ?");
                $prepare->bind_param("i", Functions::$user['id']);
                $prepare->execute();
                $_SESSION['success_message'] = Functions::Translation('text.success.minecraft_unlink');
                header("Location: /user/settings");
                exit;
            }

            $new_username = $_POST['username'];
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
                    if(Functions::IsUsernameRegistered($new_username) && strcmp($new_username, Functions::$user['username'])) {
                        $error = 1;
                        if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                        $error_msg = '- The new username ('.$new_username.') is already taken by another Account!';
                    }
                    else {
                        if(Functions::UserCanChangeName(Functions::$user['id'])) {
                            if(strcmp($new_username, Functions::$user['username'])) {
                                Functions::AddProfileEditLog(Functions::$user['id'], Functions::$user['id'], 1,'Username', Functions::$user['username'], $new_username);
                                $prepare = Functions::$mysqli->prepare("UPDATE web_users SET username = ?,last_username_change = ? WHERE id = ?");
                                $time = gmdate('U');
                                $prepare->bind_param("sii", $new_username, $time, Functions::$user['id']);
                                $prepare->execute();
                                $changed ++;
                            }
                        }
                        else {
                            $error = 1;
                            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                            $error_msg = '- You can not change your username yet!';
                        }
                    }
                }
            }

            if(!Functions::LanguageExists($new_language)) {
                $error = 1;
                if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                $error_msg = '- The selected language does not exist!';
            }
            else {
                if(Functions::$user['language'] != $new_language) {
                    Functions::AddProfileEditLog(Functions::$user['id'], Functions::$user['id'], 1,'Language', Functions::$user['language'], $new_language);
                    $prepare = Functions::$mysqli->prepare("UPDATE web_users SET language = ? WHERE id = ?");
                    $prepare->bind_param("si", $new_language, Functions::$user['id']);
                    $prepare->execute();
                    $changed ++;
                }
            }

            if($new_show_mc_name != Functions::$user['show_mc_name'] && ($new_show_mc_name == 0 || $new_show_mc_name == 1)) {
                Functions::AddProfileEditLog(Functions::$user['id'], Functions::$user['id'], 1,'Show MC-Name', Functions::$user['show_mc_name'], $new_show_mc_name);
                $prepare = Functions::$mysqli->prepare("UPDATE web_users SET show_mc_name = ? WHERE id = ?");
                $prepare->bind_param("si", $new_show_mc_name, Functions::$user['id']);
                $prepare->execute();
                $changed ++;
            }

            if(strlen($new_bio) > 4096) {
                $error = 1;
                if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                $error_msg = '- The entered Bio is too long (4096 Character max - including HMTL)';
            }
            else {
                if(Functions::$user['bio'] != $new_bio) {
                    Functions::AddProfileEditLog(Functions::$user['id'], Functions::$user['id'], 1,'Bio', Functions::$user['bio'], $new_bio);
                    $prepare = Functions::$mysqli->prepare("UPDATE web_users SET bio = ? WHERE id = ?");
                    $prepare->bind_param("si", $new_bio, Functions::$user['id']);
                    $prepare->execute();
                    $changed ++;
                }
            }

            if($error == 1) {
                $_SESSION['error_title'] = 'Edit Profile';
                $_SESSION['error_message'] = $error_msg;
            }
            if($changed > 0) {
                $_SESSION['success_message'] = 'Profile edited successfully';
            }
            header("Location: /user/settings");
            exit;
        }
        else {
            $_SESSION['error_title'] = 'Edit User';
            $_SESSION['error_message'] = 'An error occured while editing your account. Please try again! (3)';
            header("Location: /user/settings");
            exit;
        }
    }
    else {
        $_SESSION['error_title'] = 'Edit User';
        $_SESSION['error_message'] = 'An error occured while editing your account. Please try again! (2)';
        header("Location: /user/settings");
        exit;
    }
}
else {
    $_SESSION['error_title'] = 'Edit User';
    $_SESSION['error_message'] = 'An error occured while editing your account. Please try again! (1)';
    header("Location: /user/settings");
    exit;
}