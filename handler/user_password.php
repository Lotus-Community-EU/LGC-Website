<?php
$ref = $_SERVER['HTTP_REFERER'];
if(strpos($ref, Functions::$website_url) == 0) {
    if(Functions::CheckCSRF($_POST['token'])) {

        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $repeat_new_password = $_POST['repeat_new_password'];
        $error = 0; $error_msg = '';

        if(Functions::HashPassword($current_password) !== Functions::$user['password']) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= '- The input password is incorrect!';
        }

        if(strlen($new_password) < 6 || strlen($new_password) > 64) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= '- The new password has to be between 1 and 64 characters!';
        }

        if($new_password !== $repeat_new_password) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= '- The input new passwords don\'t match!';
        }

        if($error == 1) {
            $_SESSION['error_title'] = 'Change Password';
            $_SESSION['error_message'] = $error_msg;
            header("Location: /user/settings/password");
            exit;
        }

        $hashed_password = Functions::HashPassword($new_password);
        $prepare = Functions::$mysqli->prepare("UPDATE web_users SET password = ? WHERE id = ?");
        $prepare->bind_param("si", $hashed_password, Functions::$user['id']);
        $prepare->execute();
        $_SESSION['success_message'] = 'Password changed successfully!';
        header("Location: /user/settings/password");
        exit;
    }
    else {
        $_SESSION['error_title'] = 'Change Password';
        $_SESSION['error_message'] = 'An error occured while logging in. Please try again! (2)';
        header("Location: /user/settings/password");
        exit;
    }
}
else {
    $_SESSION['error_title'] = 'Change Password';
    $_SESSION['error_message'] = 'An error occured while logging in. Please try again! (1)';
    header("Location: /user/settings/password");
    exit;
}