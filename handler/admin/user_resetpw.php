<?php
$user_id = $GET['2'];

$ref = $_SERVER['HTTP_REFERER'];
if(strpos($ref, Functions::$website_url) == 0) {
    if(Functions::CheckCSRF($_POST['token'])) {
        //AddLog($staff_id, $user_id, $category, $visibility, $text)

        $user_data = Functions::GetUserData($user_id);

        $user_ranks = Functions::GetUserRanks($user_id);
        $all_ranks = Functions::GetAllRanks();
        if(($all_ranks[$user_ranks[0]]['is_staff'] == 1 || $all_ranks[$user_ranks[1]]['is_staff']) && !Functions::UserHasPermission('admin_staff_management')) {
            $_SESSION['error_title'] = 'Permissions - Edit Staff';
            $_SESSION['error_message'] = Functions::Translation('error.edit_staff');
            header("Location: /admin/user/list");
            exit;
        }
        if(($all_ranks[$user_ranks[0]]['is_upperstaff'] == 1 || $all_ranks[$user_ranks[1]]['is_upperstaff']) && !Functions::UserHasPermission('admin_upperstaff_management')) {
            $_SESSION['error_title'] = 'Permissions - Edit Upper-Staff';
            $_SESSION['error_message'] = Functions::Translation('error.edit_upperstaff');
            header("Location: /admin/user/list");
            exit;
        }

        $new_password = Functions::GeneratePassword();
        $reset_time = date('d.m.Y - H:i', gmdate('U'));

        $to = $user_data['email'];
        $subject = Functions::GetSetting('password_reset_email.subject');
        $message = Functions::GetSetting('password_reset_email.text');
        $message = str_replace('%staff_member%', Functions::$user['username'], $message);
        $message = str_replace('%new_password%', $new_password, $message);
        $message = str_replace('%reset_time%', $reset_time, $message);

        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=iso-8859-1';
        $headers[] = 'To: '.$user_data['username'].' <'.$user_data['email'].'>';
        $headers[] = 'From: Lotus Gaming Community';

        $mail_send = mail($to, $subject, $message, implode("\r\n", $headers));

        if($mail_send == true) {
            $new_password = Functions::HashPassword($new_password);
            Functions::$mysqli->query("UPDATE web_users SET password = '".$new_password."', login_token = '' WHERE id = '".$user_data['id']."'");
            Functions::AddLog(Functions::$user['id'], $user_data['id'],'user_edit', 0,'Reseted password');
            $_SESSION['success_message'] = 'Password reseted successfully!';
        }
        else {
            $_SESSION['error_title'] = 'Reset Password';
            $_SESSION['error_message'] = 'The password could not be reseted, E-Mail couldn\'t be send!';
        }
        header("Location: /admin/user/edit/".$user_id);
        exit;
    }
    else {
        $_SESSION['error_title'] = 'Edit User';
        $_SESSION['error_message'] = 'An error occured while logging in. Please try again! (2)';
        header("Location: /admin/user/edit".$user_id);
        exit;
    }
}
else {
    $_SESSION['error_title'] = 'Edit User';
    $_SESSION['error_message'] = 'An error occured while logging in. Please try again! (1)';
    header("Location: /admin/user/edit".$user_id);
    exit;
}