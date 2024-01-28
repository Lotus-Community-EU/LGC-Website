<?php
$user_id = $GET['2'];

$ref = $_SERVER['HTTP_REFERER'];
$id = $_POST['user_id']; $s_id = $_SESSION['user_id']; unset($_SESSION['user_id']);
if(strpos($ref, Functions::$website_url) == 0) {
    if(Functions::CheckCSRF($_POST['token'])) {
        if($id == $s_id) {
            //AddLog($staff_id, $user_id, $category, $visibility, $text)

            $user_data = new User($id);
            $main_rank = new Rank($user_data->getMainRank());
            $secondary_rank = new Rank($user_data->getSecondaryRank());

            
            if(($main_rank->getIsStaff() == 1 || $secondary_rank->getIsStaff() == 1) && !$user->hasPermission('admin_staff_management')) {
                $_SESSION['error_title'] = 'Permissions - Edit Staff';
                $_SESSION['error_message'] = Functions::Translation('text.error.edit_staff');
                header("Location: /admin/user/list");
                exit;
            }
            if(($main_rank->getIsUpperStaff() == 1 || $secondary_rank->getIsUpperStaff()) && !$user->hasPermission('admin_upperstaff_management')) {
                $_SESSION['error_title'] = 'Permissions - Edit Upper-Staff';
                $_SESSION['error_message'] = Functions::Translation('text.error.edit_upperstaff');
                header("Location: /admin/user/list");
                exit;
            }

            $new_password = Functions::GeneratePassword();
            $reset_time = date('d.m.Y - H:i', gmdate('U'));

            $to = $user_data->getEmail();
            $subject = $settings->getPasswordResetSubject();
            $message = $settings->getPasswordResetText();
            $message = str_replace('%staff_member%', $user->getUsername(), $message);
            $message = str_replace('%new_password%', $new_password, $message);
            $message = str_replace('%reset_time%', $reset_time, $message);

            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=iso-8859-1';
            $headers[] = 'To: '.$user_data['username'].' <'.$user_data->getEmail().'>';
            $headers[] = 'From: Lotus Gaming Community';

            $mail_send = mail($to, $subject, $message, implode("\r\n", $headers));

            if($mail_send == true) {
                $new_password = Functions::HashPassword($new_password);
                Functions::$mysqli->query("UPDATE web_users SET password = '".$new_password."', login_token = '' WHERE id = '".$user_data->getID()."'");

                $log = new Log();
                $log->setCategory('Profile_Edit');
                $log->setUser($user->getID())->setTarget($user_data->getID());
                $log->setChangedWhat('Password-Reset')->setChangedOld('0')->setChangedNew('Reseted Password');
                $log->setTime(gmdate('U'));
                $log->create();

                $_SESSION['success_message'] = 'Password reseted successfully!';
            }
            else {
                $_SESSION['error_title'] = 'Edit User - Reset Password';
                $_SESSION['error_message'] = 'The password could not be reseted, E-Mail couldn\'t be send!';
            }
            header("Location: /admin/user/edit/".$user_data->getID());
            exit;
        }
        else {
            $_SESSION['error_title'] = 'Edit User - Reset Password';
            $_SESSION['error_message'] = 'An error occured while reseting the user\'s password. Please try again! (3)';
            header("Location: /admin/user/edit".$id);
            exit;
        }
    }
    else {
        $_SESSION['error_title'] = 'Edit User - Reset Password';
        $_SESSION['error_message'] = 'An error occured while reseting the user\'s password. Please try again! (2)';
        header("Location: /admin/user/edit".$id);
        exit;
    }
}
else {
    $_SESSION['error_title'] = 'Edit User - Reset Password';
    $_SESSION['error_message'] = 'An error occured while reseting the user\'s password. Please try again! (1)';
    header("Location: /admin/user/edit".$id);
    exit;
}