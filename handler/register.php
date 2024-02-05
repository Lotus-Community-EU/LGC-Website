<?php
$ref = $_SERVER['HTTP_REFERER'];
if(strpos($ref, Functions::$website_url) == 0) {
    if(Functions::CheckCSRF('Register', $_POST['token'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $password2 = $_POST['password2'];
        $error = 0; $error_msg = '';

        if(strlen($username) < 1 || strlen($username) > 64) {
            $error = 1;
            $error_msg = '- The input Username is invalid!';
        }
        
        if(Functions::IsUsernameRegistered($username)) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= '- The Username is already taken.';
        }

        if(strlen($email) < 6 && CheckEmail($email) != true) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .= '<br>';}
            $error_msg .= '- The entered E-Mail address is invalid!';
        }

        if(strlen($password) < 6 || strlen($password) > 64) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= '- The input password has to be between 6 and 64 characters!';
        }

        if($password !== $password2) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= '- The input passwords don\'t match!';
        }

        if($error == 1) {
            $_SESSION['error_title'] = 'Register';
            $_SESSION['error_message'] = $error_msg;
            header("Location: /login/register");
            exit;
        }

        $password = Functions::HashPassword($password);
        $prepare = Functions::$mysqli->prepare("INSERT INTO web_users (username,password,email,created_at,last_username_change) VALUES (?,?,?,?,?)");
        $time = gmdate('U');
        $prepare->bind_param('sssi', $username, $password, $email, $time, $time);
        $prepare->execute();

        $row = Functions::$mysqli->insert_id;
        $login_token = Functions::CreateUniqueToken($row);
        Functions::$mysqli->query("UPDATE web_users SET login_token = '".$login_token."' WHERE id = '".$row."'");
        $_SESSION['user_token'] = $login_token;
        ?><script>SetCookie('remember', <?= $login_token;?>, time()+2592000,'/');</script><?php // 30 Tage
        header("Location: /");
        exit;
    }
    else {
        $_SESSION['error_title'] = 'Register';
        $_SESSION['error_message'] = 'An error occured while logging in. Please try again! (2)';
        header("Location: /");
        exit;
    }
}
else {
    $_SESSION['error_title'] = 'Register';
    $_SESSION['error_message'] = 'An error occured while logging in. Please try again! (1)';
    header("Location: /");
    exit;
}