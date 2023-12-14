<?php
$ref = $_SERVER['HTTP_REFERER'];
if(strpos($ref, Functions::$website_url) == 0) {
    if(Functions::CheckCSRF($_POST['token'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $error = 0; $error_msg = '';

        if(strlen($username) < 1 || strlen($username) > 64) {
            $error = 1;
            $error_msg = '- The input Username is invalid!';
        }

        if(strlen($password) < 1 || strlen($password) > 64) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= '- The input password is incorrect!';
        }

        if($error == 1) {
            $_SESSION['error_title'] = 'Login';
            $_SESSION['error_message'] = $error_msg;
            header("Location: /login");
            exit;
        }

        $password = Functions::HashPassword($password);
        $statement = Functions::$mysqli->prepare("SELECT id FROM web_users WHERE username = ? AND password = ?");
        $statement->bind_param('ss', $username, $password);
        $statement->execute();
        $result = $statement->get_result();
        if($result->num_rows > 0) {
			$row = $result->fetch_array();
			$login_token = Functions::CreateUniqueToken($row['id']);
			Functions::$mysqli->query("UPDATE web_users SET login_token = '".$login_token."' WHERE id = '".$row['id']."'");
            $time = gmdate('U');
            $ip = Functions::EncryptString(Functions::GetIP());
            $user_agent = $_SERVER["HTTP_USER_AGENT"];
            Functions::$mysqli->query("INSERT INTO web_users_logins (user_id,login_time,ip_address,user_agent) VALUES ('".$row['id']."','".$time."','".$ip."','".$user_agent."')");
			$_SESSION['user_token'] = $login_token;
			if($_POST['remember'] == 'on') {
				?><script>SetCookie('remember', <?= $login_token;?>, time()+2592000,'/');</script><?php // 30 Tage
			}
			header("Location: /");
			exit;
        }
        else {
            $_SESSION['error_title'] = 'Login';
            $_SESSION['error_message'] = '- The input credentials do not match our records!';
            header("Location: /login");
            exit;
        }
    }
    else {
        $_SESSION['error_title'] = 'Login';
        $_SESSION['error_message'] = 'An error occured while logging in. Please try again! (2)';
        header("Location: /login");
        exit;
    }
}
else {
    $_SESSION['error_title'] = 'Login';
    $_SESSION['error_message'] = 'An error occured while logging in. Please try again! (1)';
    header("Location: /login");
    exit;
}